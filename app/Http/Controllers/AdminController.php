<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;
use App\Models\Post;
use App\Models\User;
use App\Models\Notification;
use App\Models\AdminActivity;
use App\Models\TicketReply;

class AdminController extends Controller
{
    /**
     * BƯỚC 1: Hiển thị danh sách Khiếu nại Cấp 2 dành cho Admin
     * Chỉ lấy các ticket được CSKH leo thang (is_escalated = true) và chưa giải quyết (status != 'resolved')
     */
    public function escalatedTickets(Request $request)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && Auth::user()->role !== 'admin')) {
            return redirect('/login')->with('error', 'Chỉ tài khoản Quản trị viên (Admin) mới có quyền truy cập trang Khiếu nại Cấp 2.');
        }

        $tickets = SupportTicket::where(function ($q) {
                $q->where('is_escalated', true)
                  ->orWhere('is_escalated', 1)
                  ->orWhere('is_escalated', 'true')
                  ->orWhere('is_escalated', '1');
            })
            ->where('status', '!=', 'resolved')
            ->with(['post', 'staff', 'user', 'replies'])
            ->latest()
            ->get();

        foreach ($tickets as $t) {
            if (!$t->post && !empty($t->post_id)) {
                $p = Post::where('_id', $t->post_id)->first() ?: Post::find($t->post_id);
                if ($p) $t->setRelation('post', $p);
            }
            if (!$t->staff && !empty($t->staff_id)) {
                $s = User::where('_id', $t->staff_id)->first() ?: User::find($t->staff_id);
                if ($s) $t->setRelation('staff', $s);
            }
        }

        return view('Admin.khieu_nai_cap_2', compact('tickets'));
    }

    /**
     * BƯỚC 1 & 3: API Hành động đặc quyền - Gỡ bài đăng vi phạm khỏi hệ thống
     * Route: POST /admin/tickets/{id}/hide-post
     */
    public function hidePost(Request $request, $id)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && Auth::user()->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.'], 403);
        }

        $ticket = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ khiếu nại.'], 404);
        }

        // Tìm bài đăng liên kết
        $postId = $ticket->post_id ?? ($ticket->target_id ?? null);
        $post = $ticket->post ?: ($postId ? (Post::where('_id', $postId)->first() ?: Post::find($postId)) : null);

        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài đăng liên quan đến đơn này.'], 404);
        }

        $reason = $request->input('reason', 'Admin gỡ bài đăng vi phạm nghiêm trọng qua kênh Khiếu nại Cấp 2.');

        // Chuyển trạng thái post thành inactive/hidden/rejected
        $post->status = 'hidden';
        $post->is_hidden = true;
        $post->reject_reason = $reason;
        $post->rejected_at = now();
        $post->rejected_by = (string)Auth::id();
        $post->save();

        // Cập nhật trạng thái bài đăng trên Ticket
        $ticket->is_post_hidden = true;
        $ticket->save();

        // Gửi thông báo cho nhân viên đăng bài (nếu có)
        $staffId = (string)($post->user_id ?? ($post->staff_id ?? ($ticket->staff_id ?? '')));
        if ($staffId) {
            Notification::create([
                'user_id'    => $staffId,
                'role'       => 'nhanvien',
                'category'   => 'post_takedown',
                'type'       => 'error',
                'title'      => 'CẢNH BÁO: Bài đăng đã bị Admin gỡ khỏi hệ thống',
                'message'    => "Bài đăng [{$post->title}] đã bị gỡ vĩnh viễn do khiếu nại nghiêm trọng (Hồ sơ #{$ticket->formatted_ticket_code}).",
                'post_id'    => (string)($post->_id ?? $post->id),
                'action_url' => url('/Overview?tab=rejected'),
                'is_read'    => false,
                'created_at' => now(),
            ]);
        }

        // Gửi thông báo cho kênh CSKH
        \App\Http\Controllers\CSKHController::notifyCskh(
            'admin_sanction',
            'Admin đã gỡ bài đăng vi phạm',
            "Admin đã áp dụng chế tài tức thì: gỡ bài đăng [{$post->title}] liên quan đến hồ sơ khiếu nại {$ticket->formatted_ticket_code}.",
            (string)($ticket->_id ?? $ticket->id),
            (string)($post->_id ?? $post->id),
            'danger'
        );

        // Ghi nhật ký AdminActivity
        if (class_exists(AdminActivity::class)) {
            AdminActivity::log(
                'hide_post',
                'post',
                'Admin gỡ bài đăng vi phạm nghiêm trọng (Cấp 2)',
                "Admin đã gỡ bài đăng [{$post->title}] liên quan đến ticket #{$ticket->formatted_ticket_code} khỏi hệ thống.",
                $post,
                ['ticket_id' => (string)($ticket->_id ?? $ticket->id), 'post_id' => (string)($post->_id ?? $post->id)]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã gỡ bài đăng khỏi hệ thống thành công!',
            'post_id' => (string)($post->_id ?? $post->id)
        ]);
    }

    /**
     * BƯỚC 1 & 3: API Hành động đặc quyền - Khóa tài khoản nhân viên sale
     * Route: POST /admin/tickets/{id}/lock-staff
     */
    public function lockStaff(Request $request, $id)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && Auth::user()->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.'], 403);
        }

        $ticket = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ khiếu nại.'], 404);
        }

        // Tìm nhân viên phụ trách từ ticket hoặc từ post
        $staff = $ticket->staff;
        if (!$staff && $ticket->staff_id) {
            $staff = User::where('_id', $ticket->staff_id)->first() ?: User::find($ticket->staff_id);
        }
        if (!$staff && $ticket->post) {
            $staffId = $ticket->post->staff_id ?? ($ticket->post->user_id ?? null);
            if ($staffId) {
                $staff = User::where('_id', $staffId)->first() ?: User::find($staffId);
            }
        }

        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tài khoản nhân viên phụ trách bài đăng này.'], 404);
        }

        $staffName = $staff->account_name ?? ($staff->username ?? $staff->email);
        $isUnlock = $request->boolean('unlock') || $request->input('action') === 'unlock';

        if ($isUnlock) {
            // MỞ KHÓA TÀI KHOẢN
            $staff->is_locked = false;
            $staff->status = 'active';
            $staff->locked_until = null;
            $staff->lock_reason = null;
            $staff->save();

            // Đánh dấu trên Ticket
            $ticket->is_staff_locked = false;
            $ticket->save();

            // Gửi thông báo mở khóa
            Notification::create([
                'user_id'    => (string)($staff->_id ?? $staff->id),
                'role'       => 'nhanvien',
                'category'   => 'account_unlocked',
                'type'       => 'success',
                'title'      => 'Tài khoản của bạn đã được mở khóa',
                'message'    => "Ban Quản Trị đã mở khóa tài khoản nhân viên của bạn. Bạn đã có thể đăng nhập và hoạt động bình thường.",
                'action_url' => url('/thong-bao'),
                'is_read'    => false,
                'created_at' => now(),
            ]);

            // Ghi nhật ký AdminActivity
            if (class_exists(AdminActivity::class)) {
                AdminActivity::log(
                    'unlock',
                    'account',
                    'Admin mở khóa tài khoản nhân viên',
                    "Admin đã mở khóa tài khoản nhân viên: {$staffName} ({$staff->email}) liên quan đến ticket #{$ticket->formatted_ticket_code}.",
                    $staff,
                    ['ticket_id' => (string)($ticket->_id ?? $ticket->id), 'staff_id' => (string)($staff->_id ?? $staff->id)]
                );
            }

            // Gửi thông báo cho kênh CSKH
            \App\Http\Controllers\CSKHController::notifyCskh(
                'admin_sanction',
                'Admin đã mở khóa tài khoản nhân viên',
                "Admin đã mở khóa cho nhân viên [{$staffName}] liên quan đến hồ sơ khiếu nại {$ticket->formatted_ticket_code}.",
                (string)($ticket->_id ?? $ticket->id),
                $ticket->post_id ?? null,
                'info'
            );

            return response()->json([
                'success' => true,
                'is_locked' => false,
                'message' => "Đã mở khóa tài khoản nhân viên [{$staffName}] thành công!",
                'staff_id' => (string)($staff->_id ?? $staff->id)
            ]);
        }

        // KHÓA TÀI KHOẢN (Đồng bộ logic: lock_reason, lock_duration, locked_until)
        $lockReason = trim((string)($request->input('lock_reason') ?? $request->input('reason') ?? "Khóa tài khoản do vi phạm nghiêm trọng quy chuẩn RentHome (Ticket Cấp 2 #{$ticket->formatted_ticket_code})"));
        $lockDuration = $request->input('lock_duration', '7');

        $isIndefinite = ($lockDuration === 'indefinite' || (int)$lockDuration >= 9999);
        if ($isIndefinite) {
            $lockedUntil = now()->addYears(100);
        } else {
            $lockedUntil = now()->addDays((int)$lockDuration);
        }

        // Cập nhật tài khoản staff
        $staff->is_locked = true;
        $staff->status = 'locked';
        $staff->lock_reason = $lockReason;
        $staff->locked_at = now();
        $staff->locked_until = $lockedUntil;
        $staff->save();

        // Đánh dấu trên Ticket
        $ticket->is_staff_locked = true;
        $ticket->save();

        $formattedUntil = \Carbon\Carbon::parse($lockedUntil)->format('d/m/Y H:i');
        $timeText = $isIndefinite ? 'đến khi mở lại (thủ công)' : "đến {$formattedUntil}";

        // Gửi thông báo đến nhân viên
        Notification::create([
            'user_id'    => (string)($staff->_id ?? $staff->id),
            'role'       => 'nhanvien',
            'category'   => 'account_locked',
            'type'       => 'warning',
            'title'      => 'Tài khoản của bạn đã bị tạm khóa',
            'message'    => "Tài khoản của bạn đã bị khóa do vi phạm khiếu nại cấp 2. Thời hạn: {$timeText}. Lý do: {$lockReason}",
            'action_url' => url('/thong-bao'),
            'is_read'    => false,
            'created_at' => now(),
        ]);

        // Gửi thông báo cho kênh CSKH
        \App\Http\Controllers\CSKHController::notifyCskh(
            'admin_sanction',
            'Admin đã khóa tài khoản nhân viên vi phạm',
            "Admin đã áp dụng hình phạt Khóa tài khoản nhân viên [{$staffName}] ({$timeText}) do vi phạm khiếu nại cấp 2 {$ticket->formatted_ticket_code}. Lý do: {$lockReason}",
            (string)($ticket->_id ?? $ticket->id),
            $ticket->post_id ?? null,
            'danger'
        );

        // Ghi nhật ký AdminActivity
        if (class_exists(AdminActivity::class)) {
            AdminActivity::log(
                'lock',
                'account',
                'Admin khóa tài khoản nhân viên vi phạm (Cấp 2)',
                "Admin đã áp dụng hình phạt Khóa tài khoản nhân viên: {$staffName} ({$staff->email}) do vi phạm khiếu nại cấp 2. Thời hạn: {$timeText}. Lý do: {$lockReason}",
                $staff,
                ['ticket_id' => (string)($ticket->_id ?? $ticket->id), 'staff_id' => (string)($staff->_id ?? $staff->id)]
            );
        }

        return response()->json([
            'success' => true,
            'is_locked' => true,
            'message' => "Đã khóa tài khoản nhân viên [{$staffName}] thành công ({$timeText})!",
            'staff_id' => (string)($staff->_id ?? $staff->id),
            'locked_until' => $formattedUntil,
            'lock_reason' => $lockReason
        ]);
    }

    /**
     * BƯỚC 3: API Kết án & Đóng Ticket
     * Route: POST /admin/tickets/{id}/resolve
     */
    public function resolveTicket(Request $request, $id)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && Auth::user()->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.'], 403);
        }

        $ticket = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ khiếu nại.'], 404);
        }

        $verdictNote = trim((string)$request->input('resolution_note', $request->input('reply_message', '')));
        if (empty($verdictNote)) {
            $verdictNote = 'Ban Quản Trị RentHome đã trực tiếp thụ lý, giải quyết và đóng hồ sơ khiếu nại cấp 2.';
        }

        // Cập nhật ticket sang resolved
        $ticket->status = 'resolved';
        $ticket->reply_message = $verdictNote;
        $ticket->resolution_note = $verdictNote;
        $ticket->resolution_action = 'admin_adjudicated';
        $ticket->resolved_by = (string)Auth::id();
        $ticket->resolved_at = now();
        $ticket->save();

        // Lưu bản ghi tin nhắn trao đổi vào ticket_replies nếu có
        if (class_exists(TicketReply::class)) {
            TicketReply::create([
                'ticket_id'  => (string)($ticket->_id ?? $ticket->id),
                'sender_id'  => (string)Auth::id(),
                'is_cskh'    => true,
                'message'    => "⚖️ [BIÊN BẢN PHÁN QUYẾT ADMIN]:\n" . $verdictNote,
                'created_at' => now(),
            ]);
        }

        // Tìm bài đăng liên quan
        $postId = $ticket->post_id ?? ($ticket->target_id ?? null);
        $post = $ticket->post ?: ($postId ? (Post::where('_id', $postId)->first() ?: Post::find($postId)) : null);
        $postTitle = $post ? ($post->title ?? 'Bài đăng') : ($ticket->post_title ?? 'bài đăng');

        // Tìm nhân viên/người đăng bài (staff_id)
        $staffId = (string)($ticket->staff_id ?? '');
        if (empty($staffId) && $post) {
            $staffId = (string)($post->user_id ?? ($post->staff_id ?? ($post->author_id ?? '')));
        }

        // 1. Gửi Thông báo cho Khách hàng (Người khiếu nại): Text cố định, không đính kèm reply_message
        $userId = $ticket->user_id;
        if ($userId) {
            Notification::create([
                'user_id'    => (string)$userId,
                'role'       => 'khachhang',
                'category'   => 'complaint_resolved',
                'type'       => 'success',
                'title'      => 'Thông báo xử lý khiếu nại',
                'message'    => "Cảm ơn bạn. Phản ánh của bạn về bài đăng [{$postTitle}] đã được Ban quản trị xác minh và xử lý hoàn tất.",
                'action_url' => url('/thong-bao-ca-nhan'),
                'is_read'    => false,
                'created_at' => now(),
            ]);
        }

        // 2. Gửi Thông báo cho Người đăng bài (staff_id): Kèm nội dung lệnh điều hành (reply_message)
        if (!empty($staffId)) {
            Notification::create([
                'user_id'    => $staffId,
                'role'       => 'nhanvien',
                'category'   => 'complaint_directive',
                'type'       => 'warning',
                'title'      => 'Chỉ thị từ Ban quản trị về bài đăng',
                'message'    => "Thông báo từ Ban quản trị về bài đăng [{$postTitle}]: " . $ticket->reply_message,
                'post_id'    => $post ? (string)($post->_id ?? $post->id) : null,
                'action_url' => $post ? url('/chitietbaidang/' . ($post->_id ?? $post->id)) : url('/Overview?tab=posts'),
                'is_read'    => false,
                'created_at' => now(),
            ]);
        }

        // 3. Thông báo cho kênh CSKH về phán quyết hoàn tất của Admin
        \App\Http\Controllers\CSKHController::notifyCskh(
            'admin_verdict',
            'Admin đã phán quyết hồ sơ leo thang',
            "Hồ sơ khiếu nại {$ticket->formatted_ticket_code} (bài đăng [{$postTitle}]) đã được Admin phân xử và hoàn tất: \"{$verdictNote}\".",
            (string)($ticket->_id ?? $ticket->id),
            $post ? (string)($post->_id ?? $post->id) : null,
            'success'
        );

        // Ghi nhật ký AdminActivity
        if (class_exists(AdminActivity::class)) {
            AdminActivity::log(
                'resolve',
                'complaint',
                'Admin phán quyết & đóng hồ sơ khiếu nại cấp 2',
                "Admin đã hoàn tất phán quyết và đóng ticket #{$ticket->formatted_ticket_code}. Nội dung biên bản: {$verdictNote}",
                $ticket,
                ['ticket_id' => (string)($ticket->_id ?? $ticket->id)]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã hoàn tất phán quyết và đóng hồ sơ khiếu nại thành công!',
            'ticket_id' => (string)($ticket->_id ?? $ticket->id)
        ]);
    }

    /**
     * API xem chi tiết Ticket qua AJAX (cho Modal)
     */
    public function getTicketDetail($id)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && Auth::user()->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $ticket = SupportTicket::with(['post', 'staff', 'user', 'replies'])->where('_id', $id)->first() ?: SupportTicket::find($id);
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy ticket.'], 404);
        }

        return response()->json([
            'success' => true,
            'ticket' => $ticket
        ]);
    }

    /**
     * Bác bỏ khiếu nại Cấp 2 & Minh oan cho Sale (Admin)
     * Route: POST /admin/tickets/{id}/reject (name: admin.ticket.reject)
     */
    public function rejectTicket(Request $request, $id)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && Auth::user()->role !== 'admin')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Chỉ Admin mới có quyền thực hiện thao tác này.'], 403);
            }
            return redirect('/login')->with('error', 'Chỉ Admin mới có quyền thực hiện thao tác này.');
        }

        $ticket = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
        if (!$ticket) {
            $complaint = Complaint::where('_id', $id)->first() ?: Complaint::find($id);
            if ($complaint) {
                $ticket = $complaint;
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Không tìm thấy ticket!'], 404);
                }
                return redirect()->back()->with('error', 'Không tìm thấy ticket!');
            }
        }

        $verdictNote = trim((string)($request->input('resolution_note')
            ?? $request->input('reply_message')
            ?? $request->input('note')
            ?? $request->input('reason')
            ?? 'Admin đã đối soát xác minh bài đăng không có sai phạm. Bác bỏ khiếu nại và minh oan cho người đăng tin.'));

        $now = now();
        $adminId = (string)Auth::id();

        // Cập nhật ticket sang resolved / rejected
        $ticket->status = 'resolved';
        $ticket->reply_message = $verdictNote;
        $ticket->resolution_note = $verdictNote;
        $ticket->resolution_action = 'admin_rejected';
        $ticket->is_valid = false;
        $ticket->resolved_by = $adminId;
        $ticket->resolved_at = $now;
        $ticket->save();

        if (class_exists(TicketReply::class)) {
            TicketReply::create([
                'ticket_id'  => (string)($ticket->_id ?? $ticket->id),
                'user_id'    => $adminId,
                'message'    => "⛔ [PHÁN QUYẾT BÁC BỎ - MINH OAN] " . $verdictNote,
                'is_staff'   => false,
                'is_admin'   => true,
                'created_at' => $now,
            ]);
        }

        $postId = $ticket->post_id ?? ($ticket->target_id ?? null);
        $post = $ticket->post ?: ($postId ? (Post::where('_id', $postId)->first() ?: Post::find($postId)) : null);
        $postTitle = $post ? ($post->title ?? 'Bài đăng') : ($ticket->post_title ?? 'bài đăng');

        // Nếu bài đăng trước đó bị ẩn tạm thời thì phục hồi lại
        if ($post && $post->status === 'hidden' && !empty($ticket->is_post_hidden)) {
            $post->status = 'approved';
            $post->save();
            $ticket->is_post_hidden = false;
            $ticket->save();
        }

        $postDetailUrl = $postId ? url('/chitietbaidang/' . ($post->_id ?? ($post->id ?? $postId))) : url('/thong-bao-ca-nhan');

        // 1. Thông báo minh oan cho nhân viên
        $staffId = (string)($ticket->staff_id ?? ($post->staff_id ?? ($post->user_id ?? '')));
        if ($staffId) {
            Notification::create([
                'user_id'    => $staffId,
                'role'       => 'nhanvien',
                'category'   => 'complaint_exonerated',
                'type'       => 'success',
                'title'      => 'Admin đã bác bỏ khiếu nại & minh oan cho bạn',
                'message'    => "Admin đã phán quyết bác bỏ khiếu nại {$ticket->formatted_ticket_code} đối với tin [{$postTitle}]. Bạn hoàn toàn được minh oan.",
                'post_id'    => $postId ? (string)($post->_id ?? ($post->id ?? $postId)) : null,
                'action_url' => $postDetailUrl,
                'is_read'    => false,
                'created_at' => $now,
            ]);
        }

        // 2. Thông báo cho người khiếu nại
        $userId = $ticket->user_id;
        if ($userId) {
            Notification::create([
                'user_id'    => (string)$userId,
                'role'       => 'khachhang',
                'category'   => 'complaint_dismissed',
                'type'       => 'info',
                'title'      => 'Kết quả phán quyết từ Ban Quản Trị RentHome',
                'message'    => "Hồ sơ khiếu nại {$ticket->formatted_ticket_code} về bài đăng \"{$postTitle}\" của bạn đã được Ban Quản Trị xem xét và bác bỏ do thông tin không chính xác.",
                'post_id'    => $postId ? (string)($post->_id ?? ($post->id ?? $postId)) : null,
                'action_url' => $postDetailUrl,
                'is_read'    => false,
                'created_at' => $now,
            ]);
        }

        // 3. Thông báo cho kênh CSKH về phán quyết bác bỏ của Admin
        \App\Http\Controllers\CSKHController::notifyCskh(
            'admin_rejected',
            'Admin đã bác bỏ khiếu nại leo thang',
            "Admin đã xem xét và bác bỏ khiếu nại {$ticket->formatted_ticket_code} đối với bài đăng \"{$postTitle}\" (Minh oan cho nhân viên).",
            (string)($ticket->_id ?? $ticket->id),
            $postId ? (string)($post->_id ?? ($post->id ?? $postId)) : null,
            'info'
        );

        // 4. Ghi log hoạt động Admin
        AdminActivity::log(
            'reject_complaint',
            'support_ticket',
            'Admin Bác bỏ khiếu nại cấp 2 & Minh oan cho Sale',
            "Admin đã bác bỏ khiếu nại #{$ticket->formatted_ticket_code} và minh oan cho nhân viên quản lý bài đăng [{$postTitle}]. Biên bản: {$verdictNote}",
            $ticket,
            ['ticket_id' => (string)($ticket->_id ?? $ticket->id)]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã bác bỏ khiếu nại và minh oan cho Sale thành công!',
                'ticket_id' => (string)($ticket->_id ?? $ticket->id)
            ]);
        }

        return redirect()->back()->with('success', 'Đã bác bỏ khiếu nại và minh oan cho Sale thành công!');
    }
}

