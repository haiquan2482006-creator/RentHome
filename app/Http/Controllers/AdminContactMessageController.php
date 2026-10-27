<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminContactMessageController extends Controller
{
    /**
     * Kiểm tra quyền Admin
     */
    private function checkAdminPermission()
    {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        return ($user->role === 'admin' 
            || (method_exists($user, 'isAdmin') && $user->isAdmin())
            || (method_exists($user, 'isCskh') && $user->isCskh())
            || in_array($user->role, ['chăm sóc kh', 'chamsockh', 'cskh', 'cham_soc_kh']));
    }

    /**
     * Màn hình Quản lý Liên hệ (Ticket Management) dành riêng cho Admin
     */
    public function index(Request $request)
    {
        return redirect('/cskh/khieu-nai?loai=lien_he');
    }

    /**
     * Cập nhật nhanh trạng thái trực tiếp trên bảng qua AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        if (!$this->checkAdminPermission()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,resolved',
        ]);

        $ticket = ContactMessage::where('_id', $id)->orWhere('id', $id)->first();
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu liên hệ không tồn tại hoặc đã bị xóa.'
            ], 404);
        }

        $newStatus = $request->input('status');
        $ticket->status = $newStatus;

        if ($newStatus === 'resolved') {
            $ticket->responded_at = Carbon::now();
            $ticket->responded_by = (string)Auth::id();
        }

        if ($request->filled('admin_note')) {
            $ticket->admin_note = trim($request->input('admin_note'));
        }

        $ticket->save();

        // Tính lại số lượng KPI để cập nhật realtime
        $kpi = [
            'total'      => ContactMessage::count(),
            'pending'    => ContactMessage::where('status', 'pending')->count(),
            'processing' => ContactMessage::where('status', 'processing')->count(),
            'resolved'   => ContactMessage::where('status', 'resolved')->count(),
        ];

        return response()->json([
            'success'            => true,
            'message'            => 'Đã cập nhật trạng thái yêu cầu ' . $ticket->formatted_ticket_code . ' sang "' . $ticket->status_label . '" thành công!',
            'status'             => $newStatus,
            'status_label'       => $ticket->status_label,
            'status_color_class' => $ticket->status_color_class,
            'kpi'                => $kpi,
        ]);
    }

    /**
     * Lấy chi tiết ticket để hiển thị trong Modal Popup
     */
    public function show($id)
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập.'], 403);
        }

        $ticket = ContactMessage::where('_id', $id)->orWhere('id', $id)->first();
        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket không tồn tại.'], 404);
        }

        return response()->json([
            'success' => true,
            'ticket'  => [
                'id'                  => (string)($ticket->_id ?? $ticket->id),
                'ticket_code'         => $ticket->formatted_ticket_code,
                'name'                => $ticket->name ?: 'Khách hàng',
                'phone'               => $ticket->phone ?: 'Chưa cung cấp',
                'email'               => $ticket->email ?: 'Chưa cung cấp',
                'subject'             => $ticket->subject ?: 'Không có chủ đề',
                'message'             => $ticket->message ?: 'Không có nội dung tin nhắn.',
                'status'              => $ticket->status,
                'status_label'        => $ticket->status_label,
                'status_color_class'  => $ticket->status_color_class,
                'created_at'          => $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i:s') : 'N/A',
                'created_at_human'    => $ticket->created_at ? $ticket->created_at->diffForHumans() : '',
                'responded_at'        => $ticket->responded_at ? $ticket->responded_at->format('d/m/Y H:i') : null,
                'admin_note'          => $ticket->admin_note ?? '',
            ]
        ]);
    }

    /**
     * Tiếp nhận dữ liệu gửi từ form liên hệ khách hàng (API công khai)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:100',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:3000',
        ], [
            'name.required'    => 'Vui lòng nhập họ và tên của bạn.',
            'phone.required'   => 'Vui lòng nhập số điện thoại để chúng tôi liên hệ.',
            'message.required' => 'Vui lòng nhập nội dung lời nhắn cần hỗ trợ.',
        ]);

        // Tạo mã ticket tự động: #TK-001, #TK-002,...
        $countToday = ContactMessage::count();
        $ticketCode = '#TK-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

        $contact = ContactMessage::create([
            'ticket_code' => $ticketCode,
            'name'        => trim($request->input('name')),
            'phone'       => trim($request->input('phone')),
            'email'       => trim($request->input('email', '')),
            'subject'     => trim($request->input('subject') ?: 'Yêu cầu hỗ trợ từ website'),
            'message'     => trim($request->input('message')),
            'status'      => 'pending',
            'user_id'     => Auth::check() ? (string)Auth::id() : null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Cảm ơn bạn! Yêu cầu của bạn đã được gửi thành công. Đội ngũ CSKH sẽ liên hệ lại sớm nhất.',
                'ticket_code' => $ticketCode,
            ]);
        }

        return back()->with('success', 'Yêu cầu liên hệ đã được gửi thành công (Mã ticket: ' . $ticketCode . '). Chúng tôi sẽ phản hồi trong thời gian sớm nhất!');
    }

    /**
     * Xóa ticket (dành cho Admin dọn rác dữ liệu thử nghiệm)
     */
    public function destroy($id)
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa.'], 403);
        }

        $ticket = ContactMessage::where('_id', $id)->orWhere('id', $id)->first();
        if ($ticket) {
            $ticket->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa yêu cầu liên hệ thành công.'
        ]);
    }
}
