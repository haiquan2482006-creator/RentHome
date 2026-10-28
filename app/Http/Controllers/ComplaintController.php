<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Complaint;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\User;
use App\Models\Notification;
use App\Models\AdminActivity;
use App\Models\TicketReply;
use App\Models\SupportTicket;

class ComplaintController extends Controller
{
    /**
     * Màn hình danh sách Khiếu nại & Liên hệ dành cho Chăm sóc khách hàng (CSKH)
     * Gộp chung dữ liệu từ Form "Liên hệ chung" (Ký gửi, Hợp tác) và "Khiếu nại/Phản ánh bài đăng"
     * View: cham_soc_kh.khieu_nai
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || (!$user->isCskh() && !$user->isAdmin() && $user->role !== 'admin' && !$user->isModerator())) {
            return redirect('/')->with('error', 'Chỉ tài khoản Chăm sóc khách hàng mới có quyền truy cập.');
        }

        // BƯỚC 1: Lấy các tham số lọc từ URL (Mặc định: 'khieu_nai')
        $loai = 'khieu_nai';
        $status = $request->query('status');
        $keyword = trim((string)$request->query('keyword', ''));

        // 1. Thống kê KPI tổng hợp (Đơn chờ xử lý của CSKH không bao gồm các đơn đã trình lên Admin)
        $complaintPendingCount = Complaint::where('status', '!=', 'resolved')
                                          ->where('status', '!=', 'escalated')
                                          ->where('is_escalated', '!=', true)
                                          ->count()
                               + SupportTicket::where('type', 'khieu_nai')
                                              ->where('status', '!=', 'resolved')
                                              ->where('status', '!=', 'escalated')
                                              ->where('is_escalated', '!=', true)
                                              ->count();
        $totalPendingCount     = $complaintPendingCount;
        $totalResolvedCount    = Complaint::where('status', 'resolved')->count()
                               + SupportTicket::where('type', 'khieu_nai')->where('status', 'resolved')->count();

        $totalComplaintCount = Complaint::count()
                             + SupportTicket::where('type', 'khieu_nai')->count();
        $totalAllCount       = $totalComplaintCount;

        $stats = [
            'pending_complaints'  => $totalPendingCount,
            'resolved_complaints' => $totalResolvedCount,
            'total_complaints'    => $totalAllCount,
            'count_all'           => $totalAllCount,
            'count_complaint'     => $totalComplaintCount,
            'count_contact'       => 0,
            'pending_posts'       => Post::where('status', 'pending')->count(),
        ];

        // 2. Query Eloquent cho Complaint (Phản ánh & Khiếu nại bài đăng)
        $complaintQuery = Complaint::with(['user', 'post', 'staff'])
            ->when($status, function ($query, $status) {
                if ($status === 'pending') {
                    return $query->where('status', '!=', 'resolved')
                                 ->where('status', '!=', 'escalated')
                                 ->where('is_escalated', '!=', true);
                } elseif ($status === 'resolved') {
                    return $query->where('status', 'resolved');
                } elseif ($status === 'escalated') {
                    return $query->where(function ($q) {
                        $q->where('status', 'escalated')->orWhere('is_escalated', true);
                    });
                }
                return $query;
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('complaint_code', 'like', "%{$keyword}%")
                      ->orWhere('content', 'like', "%{$keyword}%")
                      ->orWhere('reason', 'like', "%{$keyword}%")
                      ->orWhere('sender_name', 'like', "%{$keyword}%")
                      ->orWhere('sender_phone', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('created_at', 'desc');

        // 3. Lấy danh sách Khiếu nại / Phản ánh bài đăng từ SupportTicket (support_tickets) và Complaint (complaints)
        $supportComplaintsQuery = SupportTicket::where('type', 'khieu_nai')
                ->with(['user', 'post', 'staff'])
                ->when($status, function ($query, $status) {
                    if ($status === 'pending') {
                        return $query->where('status', '!=', 'resolved')
                                     ->where('status', '!=', 'escalated')
                                     ->where('is_escalated', '!=', true);
                    } elseif ($status === 'resolved') {
                        return $query->where('status', 'resolved');
                    } elseif ($status === 'escalated') {
                        return $query->where(function ($q) {
                            $q->where('status', 'escalated')->orWhere('is_escalated', true);
                        });
                    }
                    return $query;
                })
                ->when($keyword, function ($query, $keyword) {
                    return $query->where(function ($q) use ($keyword) {
                        $q->where('complaint_code', 'like', "%{$keyword}%")
                          ->orWhere('ticket_code', 'like', "%{$keyword}%")
                          ->orWhere('content', 'like', "%{$keyword}%")
                          ->orWhere('message', 'like', "%{$keyword}%")
                          ->orWhere('reason', 'like', "%{$keyword}%")
                          ->orWhere('sender_name', 'like', "%{$keyword}%")
                          ->orWhere('sender_phone', 'like', "%{$keyword}%");
                    });
                })
                ->orderBy('created_at', 'desc');

            $supportComplaints = $supportComplaintsQuery->get()->map(function ($item) {
                $item->ticket_type = 'complaint';
                return $item;
            });

            $complaintItems = $complaintQuery->get()->map(function ($item) {
                $item->ticket_type = 'complaint';
                return $item;
            });

            $tickets = $supportComplaints->concat($complaintItems)->sortByDesc(function ($item) {
                return $item->created_at ? $item->created_at->timestamp : 0;
            })->values();

        $items = $tickets;
        $complaints = $tickets;
        $pendingCount = $totalPendingCount;
        $complaintCount = $totalPendingCount;

        return view('cham_soc_kh.khieu_nai', compact(
            'tickets',
            'items',
            'complaints',
            'stats',
            'pendingCount',
            'complaintCount',
            'loai',
            'status',
            'keyword'
        ));
    }

    /**
     * Xử lý giải quyết khiếu nại (Dành cho Chăm sóc khách hàng / Quản trị viên)
     * Endpoint: POST /api/cskh/complaints/{id}/resolve
     * Payload JSON: { "action": "remind_staff|hide_post|dismiss", "note": "..." }
     */
    public function resolve(Request $request, $id)
    {
        // 1. Kiểm tra quyền của người dùng (Kiểm duyệt viên, Quản trị viên, hoặc Chăm sóc khách hàng)
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isModerator() && !$currentUser->isAdmin() && !$currentUser->isCskh())) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác kiểm duyệt khiếu nại này.'
            ], 403);
        }

        // 2. Validate dữ liệu đầu vào
        $request->validate([
            'action' => 'required|string|in:remind_staff,hide_post,dismiss,resolve_contact',
            'note'   => 'nullable|string|max:1000',
        ]);

        $action = $request->input('action');
        $note = trim((string)$request->input('note', ''));
        $adminId = (string)($currentUser->_id ?? $currentUser->id);

        // 3. Tìm hồ sơ khiếu nại trong database
        $complaint = Complaint::where('_id', $id)->first() ?: Complaint::find($id);
        if (!$complaint) {
            $ticketComplaint = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
            if ($ticketComplaint && ($ticketComplaint->type ?? '') === 'khieu_nai') {
                $complaint = $ticketComplaint;
            }
        }
        if (!$complaint) {
            $contact = ContactMessage::where('_id', $id)->first() ?: ContactMessage::find($id);
            if (!$contact) {
                $contact = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
            }
            if ($contact) {
                $contact->status = 'resolved';
                $contact->admin_note = $note;
                $contact->resolution_note = $note;
                $contact->resolved_at = now();
                $contact->resolved_by = $adminId;
                $contact->save();

                $actorRole = ($currentUser->isCskh() || in_array($currentUser->role ?? '', ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh'])) ? 'Chăm sóc khách hàng (CSKH)' : ($currentUser->isModerator() ? 'Kiểm duyệt viên' : 'Quản trị viên');

                AdminActivity::log(
                    'update',
                    'contact',
                    'Xử lý yêu cầu liên hệ / hợp tác',
                    "{$actorRole} đã xử lý ticket liên hệ [{$contact->formatted_ticket_code}] của khách hàng {$contact->name}. Ghi chú: {$note}",
                    $contact,
                    [
                        'ticket_id' => (string)($contact->_id ?? $contact->id),
                        'code'      => $contact->formatted_ticket_code,
                        'action'    => $action,
                        'note'      => $note,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Đã lưu kết quả xử lý yêu cầu liên hệ thành công!',
                    'ticket'  => $contact
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hồ sơ khiếu nại hoặc yêu cầu liên hệ trên hệ thống.'
            ], 404);
        }

        // Tìm Bài đăng (Post) liên quan (qua quan hệ post hoặc post_id/target_id)
        $post = null;
        if (method_exists($complaint, 'post') && $complaint->post) {
            $post = $complaint->post;
        } elseif (!empty($complaint->post_id)) {
            $post = Post::where('_id', $complaint->post_id)->first() ?: Post::find($complaint->post_id);
        } elseif (!empty($complaint->target_id)) {
            $post = Post::where('_id', $complaint->target_id)->first() ?: Post::find($complaint->target_id);
        }

        // Tìm Nhân viên phụ trách (Staff)
        $staff = null;
        if (method_exists($complaint, 'staff') && $complaint->staff) {
            $staff = $complaint->staff;
        } elseif (!empty($complaint->staff_id)) {
            $staff = User::where('_id', $complaint->staff_id)->first() ?: User::find($complaint->staff_id);
        } elseif ($post) {
            $staffId = $post->staff_id ?? $post->user_id ?? $post->author_id;
            if ($staffId) {
                $staff = User::where('_id', $staffId)->first() ?: User::find($staffId);
            }
        }

        // Tìm Người gửi khiếu nại (Sender)
        $sender = null;
        if (method_exists($complaint, 'sender') && $complaint->sender) {
            $sender = $complaint->sender;
        } elseif (method_exists($complaint, 'user') && $complaint->user) {
            $sender = $complaint->user;
        } elseif (!empty($complaint->user_id)) {
            $sender = User::where('_id', $complaint->user_id)->first() ?: User::find($complaint->user_id);
        }

        $postTitle = $post ? ($post->title ?? 'Bài đăng phòng') : ($complaint->post_title ?? 'Bài đăng');
        $complaintCode = $complaint->complaint_code ?? ('KN-' . substr((string)($complaint->_id ?? $complaint->id), -6));

        // 4. Sử dụng DB::transaction để đảm bảo an toàn & toàn vẹn dữ liệu
        try {
            DB::transaction(function () use ($complaint, $post, $staff, $sender, $action, $note, $adminId, $postTitle, $complaintCode) {
                $now = now();

                $actorRole = ($currentUser->isCskh() || in_array($currentUser->role ?? '', ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh'])) ? 'Chăm sóc khách hàng (CSKH)' : ($currentUser->isModerator() ? 'Kiểm duyệt viên' : 'Quản trị viên');

                switch ($action) {
                    // ========================================================
                    // NHÁNH 1: Nhắc nhở nhân viên (remind_staff)
                    // ========================================================
                    case 'remind_staff':
                        // 1. Cập nhật Complaint
                        $complaint->status = 'resolved';
                        $complaint->reply_message = $note;
                        $complaint->resolution_note = $note;
                        $complaint->resolution_action = 'remind_staff';
                        $complaint->resolved_by = $adminId;
                        $complaint->resolved_at = $now;
                        $complaint->save();

                        // 2. Gửi Notification trong hệ thống cho Nhân viên phụ trách (Staff)
                        $staffId = $staff ? (string)($staff->_id ?? $staff->id) : ($post ? (string)($post->staff_id ?? $post->user_id) : null);
                        if ($staffId) {
                            $directiveMsg = $note ? ("Thông báo từ Ban quản trị về bài đăng [{$postTitle}]: " . $note) : ("Thông báo từ Ban quản trị về bài đăng [{$postTitle}]: Yêu cầu kiểm tra và đính chính thông tin bài đăng.");

                            Notification::create([
                                'user_id'    => $staffId,
                                'role'       => 'nhanvien',
                                'category'   => 'complaint_directive',
                                'type'       => 'warning',
                                'title'      => 'Chỉ thị từ Ban quản trị về bài đăng',
                                'message'    => $directiveMsg,
                                'post_id'    => $post ? (string)($post->_id ?? $post->id) : null,
                                'action_url' => $post ? url('/chitietbaidang/' . ($post->_id ?? $post->id)) : url('/Overview?tab=posts'),
                                'is_read'    => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        // Gửi thông báo hệ thống fix cứng cho Khách hàng (Người khiếu nại)
                        $senderId = $sender ? (string)($sender->_id ?? $sender->id) : (string)($complaint->user_id ?? '');
                        if ($senderId) {
                            Notification::create([
                                'user_id'    => $senderId,
                                'role'       => 'khachhang',
                                'category'   => 'complaint_resolved',
                                'type'       => 'success',
                                'title'      => 'Thông báo xử lý khiếu nại',
                                'message'    => "Cảm ơn bạn. Phản ánh của bạn về bài đăng [{$postTitle}] đã được Ban quản trị xác minh và xử lý hoàn tất.",
                                'action_url' => url('/thong-bao-ca-nhan'),
                                'is_read'    => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        // 3. Ghi nhật ký hành động AdminActivity
                        AdminActivity::log(
                            'warning',
                            'complaint',
                            'Nhắc nhở nhân viên sửa bài vi phạm',
                            "{$actorRole} đã tiếp nhận khiếu nại #{$complaintCode} và gửi thông báo yêu cầu nhân viên phụ trách kiểm tra, sửa bài [{$postTitle}]. Ghi chú: {$note}",
                            $complaint,
                            [
                                'complaint_id' => (string)($complaint->_id ?? $complaint->id),
                                'action'       => 'remind_staff',
                                'post_id'      => $post ? (string)($post->_id ?? $post->id) : null,
                                'staff_id'     => $staffId,
                                'note'         => $note,
                            ]
                        );
                        break;

                    // ========================================================
                    // NHÁNH 2: Gỡ bài vi phạm (hide_post)
                    // ========================================================
                    case 'hide_post':
                        // 1. Cập nhật Complaint
                        $complaint->status = 'resolved';
                        $complaint->reply_message = $note;
                        $complaint->resolution_note = $note;
                        $complaint->resolution_action = 'hide_post';
                        $complaint->resolved_by = $adminId;
                        $complaint->resolved_at = $now;
                        $complaint->save();

                        // 2. Cập nhật Bài đăng (Post) liên quan sang 'rejected' và ẩn khỏi Home
                        if ($post) {
                            $post->status = 'rejected';
                            $post->reject_reason = "{$actorRole} đã gỡ bài do khiếu nại: " . ($note ?: 'Nội dung vi phạm tiêu chuẩn đăng tin');
                            $post->rejected_at = $now;
                            $post->rejected_by = $adminId;
                            $post->save();
                        }

                        // 3. Gửi Notification cảnh báo vi phạm cho Nhân viên phụ trách (Staff)
                        $staffId = $staff ? (string)($staff->_id ?? $staff->id) : ($post ? (string)($post->staff_id ?? $post->user_id) : null);
                        if ($staffId) {
                            $takedownMessage = "Thông báo từ Ban quản trị về bài đăng [{$postTitle}]: " .
                                               ($note ? $note : "Bài đăng đã bị gỡ vĩnh viễn khỏi Trang chủ do vi phạm quy chuẩn tiêu chuẩn đăng tin.");

                            Notification::create([
                                'user_id'    => $staffId,
                                'role'       => 'nhanvien',
                                'category'   => 'post_rejected',
                                'type'       => 'error',
                                'title'      => 'Chỉ thị từ Ban quản trị về bài đăng',
                                'message'    => $takedownMessage,
                                'post_id'    => $post ? (string)($post->_id ?? $post->id) : null,
                                'action_url' => url('/Overview?tab=rejected'),
                                'is_read'    => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        // Gửi thông báo hệ thống fix cứng cho Khách hàng (Người khiếu nại)
                        $senderId = $sender ? (string)($sender->_id ?? $sender->id) : (string)($complaint->user_id ?? '');
                        if ($senderId) {
                            Notification::create([
                                'user_id'    => $senderId,
                                'role'       => 'khachhang',
                                'category'   => 'complaint_resolved',
                                'type'       => 'success',
                                'title'      => 'Thông báo xử lý khiếu nại',
                                'message'    => "Cảm ơn bạn. Phản ánh của bạn về bài đăng [{$postTitle}] đã được Ban quản trị xác minh và xử lý hoàn tất.",
                                'action_url' => url('/thong-bao-ca-nhan'),
                                'is_read'    => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        // 4. Ghi nhật ký hành động AdminActivity
                        AdminActivity::log(
                            'reject',
                            'complaint',
                            'Gỡ bài vi phạm do khiếu nại',
                            "{$actorRole} đã xử lý khiếu nại #{$complaintCode}: Gỡ bài đăng [{$postTitle}], chuyển trạng thái sang [rejected]. Lý do: {$note}",
                            $post ?? $complaint,
                            [
                                'complaint_id' => (string)($complaint->_id ?? $complaint->id),
                                'action'       => 'hide_post',
                                'post_id'      => $post ? (string)($post->_id ?? $post->id) : null,
                                'staff_id'     => $staffId,
                                'note'         => $note,
                            ]
                        );
                        break;

                    // ========================================================
                    // NHÁNH 3: Bác đơn khiếu nại (dismiss)
                    // ========================================================
                    case 'dismiss':
                        // 1. Cập nhật Complaint
                        $complaint->status = 'resolved';
                        $complaint->resolution_note = $note;
                        $complaint->resolution_action = 'dismiss';
                        $complaint->is_valid = false; // Ghi nhận đơn không chính xác / sai sự thật
                        $complaint->resolved_by = $adminId;
                        $complaint->resolved_at = $now;
                        $complaint->save();

                        // 2. Gửi Notification phản hồi lại cho Người gửi khiếu nại (Sender)
                        $senderId = $sender ? (string)($sender->_id ?? $sender->id) : (string)($complaint->user_id ?? '');
                        if ($senderId) {
                            $dismissMessage = "Đơn khiếu nại mã [{$complaintCode}] của bạn đã được bộ phận {$actorRole} thẩm định. " .
                                              "Sau khi đối soát thông tin thực tế, hệ thống xác nhận bài đăng không có dấu hiệu vi phạm theo nội dung phản ánh. " .
                                              ($note ? "Ghi chú phản hồi: {$note}" : "Cảm ơn bạn đã đồng hành cùng RentHome.");

                            Notification::create([
                                'user_id'    => $senderId,
                                'role'       => 'khachhang',
                                'category'   => 'complaint_dismissed',
                                'type'       => 'info',
                                'title'      => 'Thông báo kết quả thẩm định khiếu nại',
                                'message'    => $dismissMessage,
                                'post_id'    => $post ? (string)($post->_id ?? $post->id) : null,
                                'action_url' => url('/thong-bao'),
                                'is_read'    => false,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        // 3. Ghi nhật ký hành động AdminActivity
                        AdminActivity::log(
                            'dismiss',
                            'complaint',
                            'Bác đơn khiếu nại sai sự thật',
                            "{$actorRole} đã bác đơn khiếu nại #{$complaintCode} do phản ánh không chính xác / sai sự thật. Ghi chú: {$note}",
                            $complaint,
                            [
                                'complaint_id' => (string)($complaint->_id ?? $complaint->id),
                                'action'       => 'dismiss',
                                'sender_id'    => $senderId,
                                'note'         => $note,
                            ]
                        );
                        break;
                }
            });

            $actionMessages = [
                'remind_staff' => 'Đã xử lý khiếu nại: Gửi nhắc nhở yêu cầu nhân viên sửa bài thành công!',
                'hide_post'    => 'Đã xử lý khiếu nại: Gỡ bài vi phạm (chuyển sang Rejected & ẩn khỏi Home) thành công!',
                'dismiss'      => 'Đã xử lý khiếu nại: Bác đơn (ghi nhận đơn sai sự thật) & phản hồi người gửi thành công!'
            ];

            return response()->json([
                'success' => true,
                'message' => $actionMessages[$action] ?? 'Đã lưu kết quả xử lý khiếu nại thành công!',
                'complaint' => $complaint
            ]);

        } catch (\Throwable $e) {
            Log::error("Lỗi khi xử lý khiếu nại ID {$id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình xử lý khiếu nại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy chi tiết đơn khiếu nại
     * Endpoint: GET /api/admin/complaints/{id}
     */
    public function show($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isModerator() && !$currentUser->isAdmin() && !$currentUser->isCskh() && $currentUser->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập.'], 403);
        }

        $complaint = Complaint::with(['user', 'post', 'staff', 'resolver'])->where('_id', $id)->first() ?: Complaint::with(['user', 'post', 'staff', 'resolver'])->find($id);
        if (!$complaint) {
            $supportTicket = SupportTicket::with(['user', 'post', 'staff', 'resolver'])->where('_id', $id)->first() ?: SupportTicket::with(['user', 'post', 'staff', 'resolver'])->find($id);
            if ($supportTicket) {
                $isContact = ($supportTicket->type === 'lien_he');
                return response()->json([
                    'success' => true,
                    'is_contact' => $isContact,
                    'complaint' => [
                        '_id'            => (string)($supportTicket->_id ?? $supportTicket->id),
                        'complaint_code' => $supportTicket->formatted_ticket_code,
                        'sender_name'    => $supportTicket->sender_name ?? ($supportTicket->user->username ?? 'Khách hàng'),
                        'sender_phone'   => $supportTicket->sender_phone ?? ($supportTicket->phone ?? ''),
                        'email'          => $supportTicket->sender_email ?? ($supportTicket->email ?? ''),
                        'reason'         => $supportTicket->subject ?? ($supportTicket->reason ?? ($isContact ? 'Liên hệ & Hợp tác' : 'Khiếu nại bài đăng')),
                        'content'        => $supportTicket->content ?? ($supportTicket->message ?? ($supportTicket->reason ?? '')),
                        'status'         => $supportTicket->status,
                        'images'         => $supportTicket->images ?? ($supportTicket->image ? [$supportTicket->image] : []),
                        'created_at'     => $supportTicket->created_at,
                    ]
                ]);
            }

            $contact = ContactMessage::where('_id', $id)->first() ?: ContactMessage::find($id);
            if ($contact) {
                return response()->json([
                    'success' => true,
                    'is_contact' => true,
                    'complaint' => [
                        '_id' => (string)($contact->_id ?? $contact->id),
                        'complaint_code' => $contact->formatted_ticket_code,
                        'sender_name' => $contact->name,
                        'sender_phone' => $contact->phone,
                        'email' => $contact->email,
                        'reason' => $contact->subject,
                        'content' => $contact->message,
                        'status' => $contact->status,
                        'created_at' => $contact->created_at,
                    ]
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Không tìm thấy khiếu nại hoặc ticket liên hệ.'], 404);
        }

        return response()->json([
            'success' => true,
            'complaint' => $complaint
        ]);
    }

    /**
     * Xóa đơn khiếu nại (Dành cho CSKH / Admin)
     * Endpoint: DELETE /api/admin/complaints/{id}
     */
    public function destroy($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isAdmin() && !$currentUser->isCskh() && $currentUser->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa khiếu nại này.'], 403);
        }

        $complaint = Complaint::where('_id', $id)->first() ?: Complaint::find($id);
        if (!$complaint) {
            $supportTicket = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
            if ($supportTicket) {
                $code = $supportTicket->formatted_ticket_code;
                $supportTicket->delete();
                AdminActivity::log(
                    'delete',
                    'complaint',
                    'Xóa hồ sơ ticket',
                    "Bộ phận CSKH đã xóa ticket #{$code}",
                    null,
                    ['ticket_id' => (string)$id, 'code' => $code]
                );
                return response()->json([
                    'success' => true,
                    'message' => "Đã xóa ticket #{$code} thành công!"
                ]);
            }

            $contact = ContactMessage::where('_id', $id)->first() ?: ContactMessage::find($id);
            if ($contact) {
                $code = $contact->formatted_ticket_code;
                $contact->delete();
                AdminActivity::log(
                    'delete',
                    'contact',
                    'Xóa yêu cầu liên hệ',
                    "Bộ phận CSKH đã xóa ticket liên hệ #{$code}",
                    null,
                    ['contact_id' => (string)$id, 'code' => $code]
                );
                return response()->json([
                    'success' => true,
                    'message' => "Đã xóa yêu cầu liên hệ #{$code} thành công!"
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ khiếu nại để xóa.'], 404);
        }

        $code = $complaint->complaint_code ?? ('KN-' . substr((string)($complaint->_id ?? $complaint->id), -6));
        $complaint->delete();

        AdminActivity::log(
            'delete',
            'complaint',
            'Xóa đơn khiếu nại',
            "Bộ phận CSKH / Quản trị viên đã xóa đơn khiếu nại #{$code}",
            null,
            ['complaint_id' => (string)$id, 'code' => $code]
        );

        return response()->json([
            'success' => true,
            'message' => "Đã xóa đơn khiếu nại #{$code} thành công!"
        ]);
    }

    /**
     * Lấy toàn bộ lịch sử trò chuyện & tin nhắn của một Ticket (Khiếu nại hoặc Liên hệ)
     * Endpoint: GET /api/cskh/tickets/{id}/chat
     */
    public function getTicketChat($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isModerator() && !$currentUser->isAdmin() && !$currentUser->isCskh() && $currentUser->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.'], 403);
        }

        $ticketType = 'complaint';
        $complaint = Complaint::with(['user', 'post', 'staff', 'resolver'])->where('_id', $id)->first() ?: Complaint::with(['user', 'post', 'staff', 'resolver'])->find($id);
        $supportTicket = null;

        if (!$complaint) {
            $supportTicket = SupportTicket::with(['user', 'post', 'staff', 'resolver'])->where('_id', $id)->first() ?: SupportTicket::with(['user', 'post', 'staff', 'resolver'])->find($id);
        }

        if ($supportTicket) {
            $isContact = ($supportTicket->type === 'lien_he');
            $ticketType = $isContact ? 'contact' : 'complaint';

            $sender = $supportTicket->user ?? (method_exists($supportTicket, 'sender') ? $supportTicket->sender : null);
            $senderName = $supportTicket->sender_name ?? ($sender->account_name ?? ($sender->username ?? ($supportTicket->name ?? 'Khách thuê / Người dùng')));
            $senderEmail = $supportTicket->sender_email ?? ($supportTicket->email ?? ($sender->email ?? ''));
            $senderPhone = $supportTicket->sender_phone ?? ($supportTicket->phone ?? ($sender->phone ?? '---'));

            $post = $supportTicket->post ?: (!empty($supportTicket->post_id) ? (Post::where('_id', $supportTicket->post_id)->first() ?: Post::find($supportTicket->post_id)) : (!empty($supportTicket->target_id) ? (Post::where('_id', $supportTicket->target_id)->first() ?: Post::find($supportTicket->target_id)) : null));
            $postId = (string)($supportTicket->post_id ?? ($supportTicket->target_id ?? ($post ? ($post->_id ?? $post->id) : '')));
            $postTitle = $post ? ($post->title ?? 'Bài đăng trên hệ thống') : null;
            $postUrl = !empty($postId) ? url('/chitietbaidang/' . $postId) : null;
            
            $staff = $supportTicket->staff ?: ($post && !empty($post->staff_id ?? $post->user_id) ? User::find($post->staff_id ?? $post->user_id) : null);
            $staffName = $staff ? ($staff->account_name ?? ($staff->username ?? null)) : null;

            $rawImages = $supportTicket->images;
            if (!is_array($rawImages)) {
                $rawImages = !empty($rawImages) ? [$rawImages] : (!empty($supportTicket->image) ? [$supportTicket->image] : []);
            }
            $formattedImages = [];
            foreach ($rawImages as $imgItem) {
                if (empty($imgItem)) continue;
                $imgStr = (string)$imgItem;
                if (str_starts_with($imgStr, 'http://') || str_starts_with($imgStr, 'https://') || str_starts_with($imgStr, 'data:')) {
                    $formattedImages[] = $imgStr;
                } elseif (str_starts_with($imgStr, '/storage/') || str_starts_with($imgStr, 'storage/')) {
                    $formattedImages[] = asset(ltrim($imgStr, '/'));
                } else {
                    $formattedImages[] = asset('storage/' . ltrim($imgStr, '/'));
                }
            }

            $ticketData = [
                'id'              => (string)($supportTicket->_id ?? $supportTicket->id),
                'code'            => $supportTicket->formatted_ticket_code,
                'type'            => $ticketType,
                'type_label'      => $isContact ? 'Liên hệ & Hợp tác' : 'Phản ánh & Khiếu nại',
                'sender_name'     => $senderName,
                'sender_email'    => $senderEmail,
                'sender_phone'    => $senderPhone,
                'sender_avatar'   => 'https://ui-avatars.com/api/?name=' . urlencode($senderName) . '&background=' . ($isContact ? '0284c7' : 'ef4444') . '&color=fff',
                'subject'         => $supportTicket->subject ?? ($supportTicket->reason ?? ($isContact ? 'Yêu cầu liên hệ / tư vấn' : 'Phản ánh vi phạm tin đăng')),
                'post_id'         => $postId ?: null,
                'post_title'      => $postTitle,
                'post_url'        => $postUrl,
                'staff_name'      => $staffName,
                'images'          => $formattedImages,
                'initial_message' => $supportTicket->content ?? ($supportTicket->message ?? ($supportTicket->reason ?? 'Không có nội dung mô tả chi tiết')),
                'initial_image'   => !empty($formattedImages) ? $formattedImages[0] : null,
                'reply_message'   => $supportTicket->reply_message ?? ($supportTicket->resolution_note ?? ($supportTicket->admin_note ?? null)),
                'resolved_at_time'=> optional($supportTicket->resolved_at ?? $supportTicket->responded_at)->format('H:i - d/m/Y') ?? null,
                'status'          => $supportTicket->status ?? 'pending',
                'is_escalated'    => !empty($supportTicket->is_escalated) || ($supportTicket->status ?? '') === 'escalated',
                'status_label'    => $supportTicket->status_label ?? ($supportTicket->status === 'resolved' ? 'Đã giải quyết' : 'Chờ xử lý'),
                'created_at_time' => optional($supportTicket->created_at)->format('H:i - d/m/Y') ?? 'Mới đây',
                'created_at_human'=> optional($supportTicket->created_at)->diffForHumans() ?? '',
            ];
        } elseif (!$complaint) {
            $contact = ContactMessage::where('_id', $id)->first() ?: ContactMessage::find($id);
            if (!$contact) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy ticket.'], 404);
            }

            $ticketType = 'contact';
            $ticketData = [
                'id'              => (string)($contact->_id ?? $contact->id),
                'code'            => $contact->formatted_ticket_code,
                'type'            => 'contact',
                'type_label'      => 'Liên hệ & Hợp tác',
                'sender_name'     => $contact->name ?? 'Khách hàng',
                'sender_email'    => $contact->email ?? '',
                'sender_phone'    => $contact->phone ?? '',
                'sender_avatar'   => 'https://ui-avatars.com/api/?name=' . urlencode($contact->name ?? 'KH') . '&background=0284c7&color=fff',
                'subject'         => $contact->subject ?? 'Yêu cầu liên hệ / tư vấn',
                'post_id'         => null,
                'post_title'      => null,
                'post_url'        => null,
                'staff_name'      => null,
                'images'          => [],
                'initial_message' => $contact->message ?? 'Không có nội dung chi tiết',
                'initial_image'   => null,
                'reply_message'   => $contact->reply_message ?? ($contact->admin_note ?? null),
                'resolved_at_time'=> optional($contact->responded_at)->format('H:i - d/m/Y') ?? null,
                'status'          => $contact->status ?? 'pending',
                'status_label'    => $contact->status_label ?? 'Chờ xử lý',
                'created_at_time' => optional($contact->created_at)->format('H:i - d/m/Y') ?? 'Mới đây',
                'created_at_human'=> optional($contact->created_at)->diffForHumans() ?? '',
            ];
        } else {
            $sender = $complaint->user ?? (method_exists($complaint, 'sender') ? $complaint->sender : null);
            $senderName = $sender->account_name ?? ($sender->username ?? 'Khách thuê / Người dùng');
            
            $post = $complaint->post;
            $postId = (string)($complaint->post_id ?? ($complaint->target_id ?? ($post ? ($post->_id ?? $post->id) : '')));
            $postTitle = $post ? ($post->title ?? 'Bài đăng trên hệ thống') : ($complaint->post_title ?? 'Bài đăng trên hệ thống');
            $postUrl = !empty($postId) ? url('/chitietbaidang/' . $postId) : null;
            $staffName = $complaint->staff->account_name ?? ($complaint->staff_name ?? ($complaint->post->user->account_name ?? null));

            // Xử lý danh sách ảnh đính kèm (hỗ trợ cả mảng images và image đơn lẻ)
            $rawImages = $complaint->images;
            if (!is_array($rawImages)) {
                $rawImages = !empty($rawImages) ? [$rawImages] : (!empty($complaint->image) ? [$complaint->image] : []);
            }
            $formattedImages = [];
            foreach ($rawImages as $imgItem) {
                if (empty($imgItem)) continue;
                $imgStr = (string)$imgItem;
                if (str_starts_with($imgStr, 'http://') || str_starts_with($imgStr, 'https://') || str_starts_with($imgStr, 'data:')) {
                    $formattedImages[] = $imgStr;
                } elseif (str_starts_with($imgStr, '/storage/') || str_starts_with($imgStr, 'storage/')) {
                    $formattedImages[] = asset(ltrim($imgStr, '/'));
                } else {
                    $formattedImages[] = asset('storage/' . ltrim($imgStr, '/'));
                }
            }

            $ticketData = [
                'id'              => (string)($complaint->_id ?? $complaint->id),
                'code'            => $complaint->complaint_code ?? ('#KN-' . strtoupper(substr((string)($complaint->_id ?? $complaint->id), -5))),
                'type'            => 'complaint',
                'type_label'      => 'Phản ánh & Khiếu nại',
                'sender_name'     => $senderName,
                'sender_email'    => $sender->email ?? '',
                'sender_phone'    => $sender->phone ?? '',
                'sender_avatar'   => $sender->avatar ?? ('https://ui-avatars.com/api/?name=' . urlencode($senderName) . '&background=ef4444&color=fff'),
                'subject'         => $complaint->reason ?? 'Phản ánh vi phạm tin đăng',
                'post_id'         => $postId,
                'post_title'      => $postTitle,
                'post_url'        => $postUrl,
                'staff_name'      => $staffName,
                'images'          => $formattedImages,
                'initial_message' => $complaint->content ?? 'Không có nội dung mô tả chi tiết',
                'initial_image'   => !empty($formattedImages) ? $formattedImages[0] : null,
                'reply_message'   => $complaint->reply_message ?? ($complaint->resolution_note ?? null),
                'resolved_at_time'=> optional($complaint->resolved_at)->format('H:i - d/m/Y') ?? null,
                'status'          => $complaint->status ?? 'pending',
                'is_escalated'    => !empty($complaint->is_escalated) || ($complaint->status ?? '') === 'escalated',
                'status_label'    => $complaint->status === 'resolved' ? 'Đã giải quyết' : ($complaint->status === 'processing' ? 'Đang xử lý' : 'Chờ xử lý'),
                'created_at_time' => optional($complaint->created_at)->format('H:i - d/m/Y') ?? 'Mới đây',
                'created_at_human'=> optional($complaint->created_at)->diffForHumans() ?? '',
            ];
        }

        // Lấy danh sách tin nhắn phản hồi từ ticket_replies
        $replies = TicketReply::where('ticket_id', (string)$id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($reply) {
                return [
                    'id'               => (string)($reply->_id ?? $reply->id),
                    'sender_id'        => (string)$reply->sender_id,
                    'sender_name'      => $reply->sender_name ?: ($reply->is_cskh ? 'CSKH RentHome' : 'Khách hàng'),
                    'sender_avatar'    => $reply->sender_avatar ?: ($reply->is_cskh ? 'https://ui-avatars.com/api/?name=CSKH&background=0284c7&color=fff' : 'https://ui-avatars.com/api/?name=KH&background=64748b&color=fff'),
                    'is_cskh'          => (bool)$reply->is_cskh,
                    'message'          => $reply->message,
                    'created_at_time'  => optional($reply->created_at)->format('H:i - d/m/Y'),
                    'created_at_human' => optional($reply->created_at)->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'ticket'  => $ticketData,
            'replies' => $replies,
        ]);
    }

    /**
     * Gửi tin nhắn phản hồi vào Ticket (Chat/Thread)
     * Endpoint: POST /api/cskh/tickets/{id}/reply
     * Payload: { "message": "...", "action": "reply_only|reply_and_close" }
     */
    public function replyTicket(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isModerator() && !$currentUser->isAdmin() && !$currentUser->isCskh() && $currentUser->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:5000',
            'action'  => 'nullable|string|in:reply_only,reply_and_close',
        ], [
            'message.required' => 'Vui lòng nhập nội dung tin nhắn phản hồi cho khách hàng.',
        ]);

        $message = trim((string)$request->input('message'));
        $action = $request->input('action', 'reply_only');
        $isClose = ($action === 'reply_and_close');

        $complaint = Complaint::where('_id', $id)->first() ?: Complaint::find($id);
        $ticketType = 'complaint';
        $ticket = $complaint;

        if (!$complaint) {
            $supportTicket = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
            if ($supportTicket) {
                $ticketType = ($supportTicket->type === 'lien_he') ? 'contact' : 'complaint';
                $ticket = $supportTicket;
            } else {
                $contact = ContactMessage::where('_id', $id)->first() ?: ContactMessage::find($id);
                if (!$contact) {
                    return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ ticket cần phản hồi.'], 404);
                }
                $ticketType = 'contact';
                $ticket = $contact;
            }
        }

        $adminId = (string)($currentUser->_id ?? $currentUser->id);
        $adminName = $currentUser->account_name ?: ($currentUser->username ?: 'Nhân viên CSKH');
        $ticketCode = method_exists($ticket, 'getFormattedTicketCodeAttribute') 
            ? $ticket->formatted_ticket_code 
            : ($ticketType === 'contact' 
                ? ($ticket->formatted_ticket_code ?? '#TK') 
                : ($ticket->complaint_code ?? ('#KN-' . strtoupper(substr((string)($ticket->_id ?? $ticket->id), -5)))));

        // 1. Tạo tin nhắn phản hồi trong collection ticket_replies
        $reply = TicketReply::create([
            'ticket_id'      => (string)$id,
            'ticket_type'    => $ticketType,
            'sender_id'      => $adminId,
            'sender_name'    => $adminName . ' (CSKH)',
            'sender_avatar'  => $currentUser->avatar ?? null,
            'is_cskh'        => true,
            'message'        => $message,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 2. Cập nhật trạng thái Ticket
        // 2. BƯỚC 4: Cập nhật trường reply_message và chuyển status = 'resolved'
        $ticket->reply_message = $message;

        if ($isClose) {
            $ticket->status = 'resolved';
            if ($ticketType === 'contact') {
                $ticket->admin_note = $message;
                $ticket->responded_at = now();
                $ticket->responded_by = $adminId;
            } else {
                $ticket->resolution_note = $message;
                $ticket->resolution_action = 'reply_resolved';
                $ticket->resolved_by = $adminId;
                $ticket->resolved_at = now();
            }
        } else {
            // Nút "Gửi phản hồi": nếu đang pending thì chuyển thành processing
            if ($ticket->status === 'pending') {
                $ticket->status = 'processing';
            }
        }
        $ticket->save();

        // 3. Tìm bài đăng & nhân viên đăng bài (staff_id)
        $post = null;
        if (method_exists($ticket, 'post') && $ticket->post) {
            $post = $ticket->post;
        } elseif (!empty($ticket->post_id)) {
            $post = Post::where('_id', $ticket->post_id)->first() ?: Post::find($ticket->post_id);
        } elseif (!empty($ticket->target_id)) {
            $post = Post::where('_id', $ticket->target_id)->first() ?: Post::find($ticket->target_id);
        }
        $postTitle = $post ? ($post->title ?? 'Bài đăng') : ($ticket->post_title ?? 'bài đăng');

        $staffId = (string)($ticket->staff_id ?? '');
        if (empty($staffId) && $post) {
            $staffId = (string)($post->user_id ?? ($post->staff_id ?? ($post->author_id ?? '')));
        }

        // 4. Gửi thông báo cho Khách hàng (Người khiếu nại): Text fix cứng, tuyệt đối KHÔNG đính kèm reply_message
        $recipientUser = null;
        if (method_exists($ticket, 'user') && $ticket->user) {
            $recipientUser = $ticket->user;
        } elseif (!empty($ticket->user_id)) {
            $recipientUser = User::where('_id', $ticket->user_id)->first() ?: User::find($ticket->user_id);
        } elseif (!empty($ticket->email)) {
            $recipientUser = User::where('email', $ticket->email)->first();
        }

        if ($recipientUser) {
            $recipientUserId = (string)($recipientUser->_id ?? $recipientUser->id);
            Notification::create([
                'user_id'    => $recipientUserId,
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

        // 5. Gửi thông báo cho Người đăng bài (staff_id): Kèm nội dung lệnh điều hành ($ticket->reply_message)
        if ($isClose && !empty($staffId)) {
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

        // 4. Ghi log AdminActivity
        AdminActivity::log(
            $isClose ? 'resolve' : 'reply',
            $ticketType,
            $isClose ? 'Phản hồi & Đóng Ticket' : 'Gửi tin nhắn phản hồi Ticket',
            "{$adminName} đã gửi phản hồi cho ticket [{$ticketCode}]: \"{$message}\"",
            $ticket,
            [
                'ticket_id' => (string)$id,
                'code'      => $ticketCode,
                'is_close'  => $isClose,
                'reply_id'  => (string)($reply->_id ?? $reply->id)
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $isClose ? 'Đã gửi phản hồi và đóng Ticket thành công!' : 'Đã gửi phản hồi thành công!',
            'reply'   => [
                'id'               => (string)($reply->_id ?? $reply->id),
                'sender_id'        => $adminId,
                'sender_name'      => $reply->sender_name,
                'sender_avatar'    => $reply->sender_avatar ?: 'https://ui-avatars.com/api/?name=CSKH&background=0284c7&color=fff',
                'is_cskh'          => true,
                'message'          => $reply->message,
                'created_at_time'  => now()->format('H:i - d/m/Y'),
                'created_at_human' => 'Vừa xong',
            ],
            'ticket'  => [
                'id'           => (string)$id,
                'status'       => $ticket->status,
                'status_label' => $ticket->status === 'resolved' ? 'Đã giải quyết' : ($ticket->status === 'processing' ? 'Đang xử lý' : 'Chờ xử lý'),
            ]
        ]);
    }

    /**
     * BƯỚC 2: Khách hàng gửi Khiếu nại / Báo cáo bài đăng (từ trang chitietbaidang.blade.php)
     * Endpoint: POST /bai-dang/{id}/khieu-nai hoặc POST /cskh/complaints/submit
     */
    public function storeCustomerComplaint(Request $request, $id = null)
    {
        $postId = $id ?: $request->input('post_id');
        $staffId = $request->input('staff_id');

        $request->validate([
            'content'   => 'required|string|min:5|max:3000',
            'reason'    => 'nullable|string|max:255',
            'images'    => 'nullable|array',
            'images.*'  => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'content.required' => 'Vui lòng nhập nội dung lý do khiếu nại.',
            'content.min'      => 'Nội dung khiếu nại tối thiểu 5 ký tự.',
            'images.*.image'   => 'File tải lên phải là hình ảnh hợp lệ.',
            'images.*.max'     => 'Kích thước mỗi ảnh tải lên tối đa 5MB.',
        ]);

        $content = trim((string)$request->input('content'));
        $reason = trim((string)$request->input('reason', 'Khiếu nại thông tin bài đăng'));

        // Xử lý upload ảnh vào storage/app/public/complaints
        $storedImages = [];
        if ($request->hasFile('images')) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('complaints');
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('complaints', 'public');
                    if ($path) {
                        $storedImages[] = $path;
                    }
                }
            }
        }

        // Lấy thông tin user gửi khiếu nại
        $currentUser = Auth::user();
        $userId = $currentUser ? (string)($currentUser->_id ?? $currentUser->id) : null;

        // Tìm thông tin bài đăng nếu có
        $post = null;
        if (!empty($postId)) {
            $post = Post::where('_id', $postId)->first() ?: Post::find($postId);
            if ($post && empty($staffId)) {
                $staffId = $post->staff_id ?? ($post->user_id ?? null);
            }
        }

        $code = 'KN-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $complaint = Complaint::create([
            'complaint_code' => $code,
            'user_id'        => $userId,
            'post_id'        => $postId ? (string)$postId : null,
            'target_id'      => $postId ? (string)$postId : null,
            'target_type'    => 'post',
            'staff_id'       => $staffId ? (string)$staffId : null,
            'content'        => $content,
            'reason'         => $reason,
            'images'         => $storedImages,
            'image'          => !empty($storedImages) ? $storedImages[0] : null,
            'status'         => 'pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Bắn thông báo về kênh CSKH
        $cskhSenderName = $currentUser ? ($currentUser->display_name ?? $currentUser->name) : 'Khách vãng lai';
        $cskhPostTitle = $post ? ($post->title ?? 'Bài đăng') : 'Bài đăng';
        \App\Http\Controllers\CSKHController::notifyCskh(
            'new_complaint',
            'Khiếu nại mới từ khách thuê',
            "Khách hàng [{$cskhSenderName}] vừa gửi khiếu nại [{$code}] về bài đăng \"{$cskhPostTitle}\". Vui lòng tiếp nhận và xử lý.",
            (string)($complaint->_id ?? $complaint->id),
            $postId ? (string)$postId : null,
            'warning'
        );

        // Ghi log hoạt động AdminActivity
        if ($currentUser) {
            AdminActivity::log(
                'create',
                'complaint',
                'Gửi khiếu nại bài đăng',
                "Khách hàng {$currentUser->display_name} đã gửi khiếu nại [{$code}] cho bài đăng: " . ($post->title ?? 'Bài đăng'),
                $complaint,
                ['post_id' => $postId, 'images_count' => count($storedImages)]
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hệ thống đã tiếp nhận khiếu nại của bạn. Bộ phận CSKH sẽ kiểm tra và phản hồi sớm nhất!',
                'complaint' => [
                    'id'   => (string)($complaint->_id ?? $complaint->id),
                    'code' => $code,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Hệ thống đã tiếp nhận khiếu nại của bạn. Bộ phận CSKH sẽ kiểm tra và phản hồi sớm nhất!');
    }

    /**
     * CSKH Trình lên Admin giải quyết (Escalate Ticket)
     * Thiết lập is_escalated = true, status = 'escalated', lưu cskh_note
     */
    public function escalateTicket(Request $request, $id)
    {
        $cskhNote = trim((string)$request->input('admin_note', $request->input('cskh_note', $request->input('reason', ''))));
        if (empty($cskhNote)) {
            $cskhNote = 'CSKH đã xác minh và trình vi phạm lên Ban Quản Trị / Admin giải quyết.';
        }

        $ticket = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
        if (!$ticket) {
            // Kiểm tra trường hợp hồ sơ từ bảng Complaint
            $complaint = Complaint::where('_id', $id)->first() ?: Complaint::find($id);
            if ($complaint) {
                $complaint->is_escalated = true;
                $complaint->status = 'escalated';
                $complaint->admin_note = $cskhNote;
                $complaint->cskh_note = $cskhNote;
                $complaint->escalation_reason = $cskhNote;
                $complaint->escalated_at = now();
                $complaint->escalated_by = (string)Auth::id();
                $complaint->save();

                // Tạo đối tượng SupportTicket tương ứng để Admin xử lý tập trung
                $ticket = SupportTicket::create([
                    'ticket_code'       => $complaint->complaint_code ?? ('#KN-' . strtoupper(substr((string)$complaint->_id, -5))),
                    'type'              => 'khieu_nai',
                    'user_id'           => $complaint->user_id,
                    'post_id'           => $complaint->post_id ?? $complaint->target_id,
                    'staff_id'          => $complaint->staff_id,
                    'sender_name'       => $complaint->sender_name ?? $complaint->name,
                    'sender_phone'      => $complaint->sender_phone ?? $complaint->phone,
                    'sender_email'      => $complaint->sender_email ?? $complaint->email,
                    'subject'           => $complaint->reason ?? 'Khiếu nại bài đăng vi phạm',
                    'content'           => $complaint->content,
                    'images'            => $complaint->images ?? ($complaint->image ? [$complaint->image] : []),
                    'status'            => 'escalated',
                    'is_escalated'      => true,
                    'admin_note'        => $cskhNote,
                    'cskh_note'         => $cskhNote,
                    'escalation_reason' => $cskhNote,
                    'escalated_at'      => now(),
                    'escalated_by'      => (string)Auth::id(),
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ ticket.'], 404);
            }
        } else {
            $ticket->is_escalated = true;
            $ticket->status = 'escalated';
            $ticket->escalated_at = now();
            $ticket->escalated_by = (string)Auth::id();
            $ticket->admin_note = $cskhNote;
            $ticket->cskh_note = $cskhNote;
            $ticket->escalation_reason = $cskhNote;
            $ticket->save();
        }

        // Tạo thông báo cho Admin
        Notification::create([
            'role'       => 'admin',
            'category'   => 'ticket_escalated',
            'type'       => 'warning',
            'title'      => 'CẢNH BÁO: Khiếu nại nghiêm trọng mới được CSKH trình lên',
            'message'    => "Hồ sơ #{$ticket->formatted_ticket_code} đã được trình lên Admin giải quyết. Ghi chú CSKH: {$cskhNote}",
            'action_url' => url('/admin/khieu-nai-cap-2'),
            'is_read'    => false,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã trình hồ sơ lên Admin giải quyết thành công!'
        ]);
    }

    /**
     * Bác bỏ khiếu nại (CSKH) - Báo cáo sai / Không vi phạm
     * Route: POST /cskh/tickets/{id}/reject (name: cskh.ticket.reject)
     */
    public function rejectTicket(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || (!$currentUser->isModerator() && !$currentUser->isAdmin() && !$currentUser->isCskh())) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
            }
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
        }

        $reason = trim((string)($request->input('reply_message')
            ?? $request->input('message')
            ?? $request->input('note')
            ?? $request->input('reason')
            ?? $request->input('resolution_note')
            ?? 'Khiếu nại không chính xác hoặc bài đăng không có dấu hiệu vi phạm (Báo cáo sai).'));

        $adminId = (string)($currentUser->_id ?? $currentUser->id);
        $actorRole = ($currentUser->isCskh() || in_array($currentUser->role ?? '', ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh'])) ? 'Chăm sóc khách hàng (CSKH)' : ($currentUser->isModerator() ? 'Kiểm duyệt viên' : 'Quản trị viên');
        $now = now();

        // 1. Tìm trong SupportTicket hoặc Complaint
        $ticket = SupportTicket::where('_id', $id)->first() ?: SupportTicket::find($id);
        $complaint = null;

        if (!$ticket) {
            $complaint = Complaint::where('_id', $id)->first() ?: Complaint::find($id);
        } else {
            $complaint = Complaint::where('_id', $id)->first() ?: Complaint::find($id);
        }

        if (!$ticket && !$complaint) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ khiếu nại.'], 404);
            }
            return redirect()->back()->with('error', 'Không tìm thấy hồ sơ khiếu nại.');
        }

        $targetObj = $ticket ?: $complaint;
        $code = $targetObj->complaint_code ?? ($targetObj->ticket_code ?? ('#KN-' . strtoupper(substr((string)($targetObj->_id ?? $targetObj->id), -5))));

        // Cập nhật trạng thái
        if ($ticket) {
            $ticket->status = 'resolved';
            $ticket->resolution_action = 'rejected';
            $ticket->is_valid = false;
            $ticket->reply_message = $reason;
            $ticket->resolution_note = $reason;
            $ticket->resolved_by = $adminId;
            $ticket->resolved_at = $now;
            $ticket->save();

            if (class_exists(TicketReply::class)) {
                TicketReply::create([
                    'ticket_id'  => (string)($ticket->_id ?? $ticket->id),
                    'user_id'    => $adminId,
                    'message'    => "⛔ [BÁC BỎ KHIẾU NẠI] " . $reason,
                    'is_staff'   => false,
                    'is_admin'   => true,
                    'created_at' => $now,
                ]);
            }
        }

        if ($complaint) {
            $complaint->status = 'resolved';
            $complaint->resolution_action = 'dismiss';
            $complaint->is_valid = false;
            $complaint->resolution_note = $reason;
            $complaint->resolved_by = $adminId;
            $complaint->resolved_at = $now;
            $complaint->save();
        }

        $postId = $targetObj->post_id ?? ($targetObj->target_id ?? null);
        $post = (method_exists($targetObj, 'post') && $targetObj->post) ? $targetObj->post : ($postId ? (\App\Models\Post::where('_id', $postId)->first() ?: \App\Models\Post::find($postId)) : null);
        $postTitle = $post ? ($post->title ?? 'Bài đăng') : ($targetObj->post_title ?? 'bài đăng');
        $postDetailUrl = $postId ? url('/chitietbaidang/' . ($post->_id ?? ($post->id ?? $postId))) : url('/thong-bao-ca-nhan');

        // Thông báo cho Người gửi khiếu nại
        $senderId = (string)($targetObj->user_id ?? ($targetObj->sender_id ?? ''));
        if ($senderId) {
            Notification::create([
                'user_id'    => $senderId,
                'role'       => 'khachhang',
                'category'   => 'complaint_dismissed',
                'type'       => 'info',
                'title'      => 'Thông báo kết quả khiếu nại',
                'message'    => "Đơn khiếu nại [{$code}] về bài đăng \"{$postTitle}\" của bạn đã được {$actorRole} thẩm định và bác bỏ. Lý do: {$reason}",
                'post_id'    => $postId ? (string)($post->_id ?? ($post->id ?? $postId)) : null,
                'action_url' => $postDetailUrl,
                'is_read'    => false,
                'created_at' => $now,
            ]);
        }

        // Thông báo cho Nhân viên sale / Người đăng bài (minh oan)
        $staffId = (string)($targetObj->staff_id ?? '');
        if (!$staffId && $post) {
            $staffId = (string)($post->staff_id ?? ($post->user_id ?? ''));
        }
        if ($staffId) {
            Notification::create([
                'user_id'    => $staffId,
                'role'       => 'nhanvien',
                'category'   => 'complaint_exonerated',
                'type'       => 'success',
                'title'      => 'Thông báo minh oan: Đơn khiếu nại đã bị bác bỏ',
                'message'    => "Hồ sơ khiếu nại [{$code}] liên quan đến bài đăng [{$postTitle}] của bạn đã được {$actorRole} kiểm tra đối soát và kết luận không vi phạm (Bác bỏ khiếu nại).",
                'post_id'    => $postId ? (string)($post->_id ?? ($post->id ?? $postId)) : null,
                'action_url' => $postDetailUrl,
                'is_read'    => false,
                'created_at' => $now,
            ]);
        }

        // Ghi log AdminActivity
        AdminActivity::log(
            'reject_complaint',
            'complaint',
            'Bác bỏ khiếu nại (Báo cáo sai)',
            "{$actorRole} đã bác bỏ khiếu nại [{$code}]. Lý do: {$reason}",
            $targetObj,
            ['ticket_id' => (string)($targetObj->_id ?? $targetObj->id), 'reason' => $reason]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã bác bỏ khiếu nại thành công!',
                'ticket_id' => (string)($targetObj->_id ?? $targetObj->id)
            ]);
        }

        return redirect()->back()->with('success', 'Đã bác bỏ khiếu nại thành công!');
    }
}

