<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\Post;
use App\Models\SupportTicket;
use App\Models\Complaint;
use App\Models\Building;
use App\Models\User;
use App\Models\AdminActivity;

class RealtimePollController extends Controller
{
    /**
     * API Real-time Short Polling
     * Lấy các dữ liệu mới nhất (thông báo, bài đăng chờ duyệt, khiếu nại, phản ánh...)
     * để tự động Prepend vào giao diện người dùng mà không cần tải lại trang.
     *
     * Route: GET /api/realtime/poll
     */
    public function poll(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'logged_in' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $userId = (string)($user->_id ?? $user->id);
        $sinceParam = $request->input('since');

        // Xác định mốc thời gian truy vấn dữ liệu mới (mặc định 8 giây gần nhất nếu client chưa gửi since)
        if (!empty($sinceParam)) {
            try {
                // Trừ hao 3 giây để tránh trôi lệch mili-giây giữa các lượt request
                $sinceTime = Carbon::parse($sinceParam)->subSeconds(3);
            } catch (\Exception $e) {
                $sinceTime = Carbon::now()->subSeconds(8);
            }
        } else {
            $sinceTime = Carbon::now()->subSeconds(8);
        }

        $serverTime = Carbon::now()->toIso8601String();

        // 1. LẤY THÔNG BÁO MỚI (DÀNH CHO TẤT CẢ CÁC TÀI KHOẢN HIỆN CÓ TRONG HỆ THỐNG)
        $notifQuery = Notification::where(function ($q) use ($userId, $user) {
            $q->where('user_id', $userId)
              ->orWhere('user_id', $user->id);

            if ($user->isAdmin() || $user->role === 'admin') {
                $q->orWhere('role', 'admin')->orWhere('target_role', 'admin');
            } elseif ($user->isModerator() || in_array($user->role, ['kiemduyet', 'moderator'])) {
                $q->orWhere('role', 'kiemduyet')
                  ->orWhere('for_role', 'moderator')
                  ->orWhere('target_role', 'moderator');
            } elseif ($user->isCskh() || in_array($user->role, ['cskh', 'cham sóc kh', 'cham_soc_kh'])) {
                $q->orWhere('role', 'cskh')->orWhere('target_role', 'cskh');
            }
        });

        // Tổng số thông báo chưa đọc
        $unreadCount = (clone $notifQuery)->where('is_read', false)->count();

        // Danh sách thông báo mới được tạo sau mốc $sinceTime
        $newNotifs = (clone $notifQuery)
            ->where('created_at', '>', $sinceTime)
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        $formattedNotifs = $newNotifs->map(function ($n) {
            $notifId = (string)($n->_id ?? $n->id);
            $data = (array)($n->data ?? []);
            $title = $n->title ?: ($data['title'] ?? 'Thông báo hệ thống');
            $message = $n->message ?: ($data['message'] ?? ($n->content ?? ''));
            $type = $n->type ?: ($data['type'] ?? 'info');
            $category = $n->category ?: ($data['category'] ?? 'system');
            $actionUrl = $n->action_url ?: ($data['action_url'] ?? null);
            $createdAt = $n->created_at ? $n->created_at->toIso8601String() : now()->toIso8601String();
            $timeAgo = $n->created_at ? $n->created_at->diffForHumans() : 'Vừa xong';

            return [
                'id' => $notifId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'category' => $category,
                'action_url' => $actionUrl,
                'created_at' => $createdAt,
                'time_ago' => $timeAgo,
                'is_read' => (bool)$n->is_read,
            ];
        });

        // 2. DỮ LIỆU ĐẶC THÙ THEO VAI TRÒ NGHIỆP VỤ (KIỂM DUYỆT, CSKH, ADMIN, USER)
        $roleData = [];

        // 2.1 KIỂM DUYỆT VIÊN: BÀI ĐĂNG PHÒNG CHỜ DUYỆT MỚI
        if ($user->isModerator() || in_array($user->role, ['kiemduyet', 'moderator'])) {
            $pendingStatuses = ['pending', 'cho_duyet', 'chờ duyệt', 'waiting', 'cho duyet'];
            $pendingPostsCount = Post::whereIn('status', $pendingStatuses)->count();

            $newPosts = Post::whereIn('status', $pendingStatuses)
                ->where(function($q) use ($sinceTime) {
                    $q->where('created_at', '>', $sinceTime)
                      ->orWhere('updated_at', '>', $sinceTime);
                })
                ->orderBy('updated_at', 'desc')
                ->take(10)
                ->get();

            $formattedPosts = $newPosts->map(function ($p) {
                $pId = (string)($p->_id ?? $p->id);
                $bld = $p->building ?? null;
                if (!$bld && !empty($p->building_id)) {
                    $bld = Building::find($p->building_id);
                }
                $author = $p->user ?? null;
                if (!$author && !empty($p->user_id)) {
                    $author = User::find($p->user_id);
                }

                $staffName = $author ? ($author->account_name ?: ($author->username ?: $author->email)) : 'Nhân viên Sale';
                $staffContact = $author ? ($author->phone ?: $author->email) : 'Chưa cập nhật';
                $staffInitial = mb_strtoupper(mb_substr($staffName, 0, 1, 'UTF-8'));

                $bldName = $bld->name ?? ($p->title ?? 'Tòa nhà / Cơ sở');
                $bldAddress = $bld->full_address ?? ($bld->address_detail ?? ($bld->province ?? 'Hà Nội'));

                $rawBType = $bld->type ?? ($bld->business_type ?? ($p->property_type ?? 'can_ho'));
                $typeMap = [
                    'can_ho'           => 'Chung cư mini / Căn hộ dịch vụ',
                    'can_ho_dich_vu'   => 'Chung cư mini / Căn hộ dịch vụ',
                    'chung_cu_mini'    => 'Chung cư mini / Căn hộ dịch vụ',
                    'nha_tro'          => 'Nhà trọ / Khu trọ',
                    'day_tro'          => 'Nhà trọ / Khu trọ',
                    'ky_tuc_xa'        => 'Ký túc xá / Sleepbox',
                    'nha_rieng'        => 'Nhà ở / Nhà riêng lẻ',
                    'nha_nguyen_can'   => 'Nhà ở / Nhà riêng lẻ',
                    'cum_nha_lien_ke'  => 'Cụm nhà liền kề / Dự án',
                    'dat_phan_lo'      => 'Đất phân lô / Đất nền',
                    'dat_nen'          => 'Mặt bằng kinh doanh / Đất nền',
                    'toa_nha_van_phong'=> 'Tòa nhà văn phòng',
                ];
                $bldType = $typeMap[$rawBType] ?? ($rawBType ?: 'Chung cư mini / Căn hộ dịch vụ');

                // Bảng giá dịch vụ Lớp Vỏ
                $bElec = (float)($bld->electricity_price ?? 0);
                $bWater = (float)($bld->water_price ?? 0);
                $bWaterType = $bld->water_calc_type ?? 'm3';
                $bService = (float)($bld->service_fee ?? 0);
                $bInternet = (float)($bld->internet_fee ?? 0);
                $bParking = (float)($bld->parking_fee ?? 0);

                $hasAnyService = ($bElec > 0 || $bWater > 0 || $bService > 0 || $bInternet > 0 || $bParking > 0);
                $hasServicePricingExplicit = isset($bld->has_service_pricing) && $bld->has_service_pricing !== null ? (bool)$bld->has_service_pricing : null;

                if ($hasServicePricingExplicit !== null) {
                    $noServicePricing = !$hasServicePricingExplicit;
                } elseif ($hasAnyService) {
                    $noServicePricing = false;
                } else {
                    $isExcludedBType = in_array($rawBType, ['dat_phan_lo', 'dat_nen', 'nha_rieng', 'nha_nguyen_can', 'cum_nha_lien_ke']);
                    $noServicePricing = $isExcludedBType || !$hasAnyService;
                }

                $bAmenities = is_array($bld->amenities ?? null) 
                    ? $bld->amenities 
                    : (is_string($bld->amenities ?? null) ? json_decode($bld->amenities, true) : []);
                $bAmenities = is_array($bAmenities) ? array_values(array_filter($bAmenities)) : [];

                $buildingImages = [];
                if (!empty($bld->images) && is_iterable($bld->images)) {
                    foreach ($bld->images as $rawBIm) {
                        $s = is_array($rawBIm) ? ($rawBIm['url'] ?? ($rawBIm['path'] ?? '')) : (string)$rawBIm;
                        if (!$s) continue;
                        if (\Illuminate\Support\Str::startsWith($s, ['http', 'data:', '/images/', '/storage/'])) {
                            $buildingImages[] = $s;
                        } elseif (preg_match('/^[a-f\d]{24}$/i', $s)) {
                            $buildingImages[] = url('/images/' . $s);
                        } else {
                            $buildingImages[] = asset('storage/' . ltrim($s, '/'));
                        }
                    }
                }
                if (!empty($bld->image)) {
                    $s = $bld->image;
                    if (\Illuminate\Support\Str::startsWith($s, ['http', 'data:', '/images/', '/storage/'])) {
                        array_unshift($buildingImages, $s);
                    } elseif (preg_match('/^[a-f\d]{24}$/i', $s)) {
                        array_unshift($buildingImages, url('/images/' . $s));
                    } else {
                        array_unshift($buildingImages, asset('storage/' . ltrim($s, '/')));
                    }
                }
                $buildingImages = array_values(array_unique($buildingImages));

                // Danh sách ảnh bài đăng phòng (Lớp Ruột)
                $postImages = [];
                if (!empty($p->all_images) && is_iterable($p->all_images)) {
                    foreach ($p->all_images as $im) {
                        $u = is_array($im) ? ($im['url'] ?? '') : ($im->url ?? '');
                        if ($u) $postImages[] = $u;
                    }
                }
                if (empty($postImages) && !empty($p->images) && is_iterable($p->images)) {
                    foreach ($p->images as $rawIm) {
                        $s = is_array($rawIm) ? ($rawIm['id'] ?? ($rawIm['url'] ?? '')) : (string)$rawIm;
                        if (!$s) continue;
                        if (\Illuminate\Support\Str::startsWith($s, ['http', 'data:', '/images/', '/storage/'])) {
                            $postImages[] = $s;
                        } elseif (preg_match('/^[a-f\d]{24}$/i', $s)) {
                            $postImages[] = url('/images/' . $s);
                        } else {
                            $postImages[] = asset('storage/' . ltrim($s, '/'));
                        }
                    }
                }
                $thumbUrl = $p->display_image ?: ($postImages[0] ?? 'https://placehold.co/160x120?text=No+Image');
                if (empty($postImages)) $postImages[] = $thumbUrl;

                $rawPrice = $p->price ?? 0;
                $pUnit = $p->price_unit ?? 'thang';
                $unitLabel = match($pUnit) {
                    'nam' => '/ năm',
                    'tong' => '(Tổng tiền)',
                    'm2' => '/ m²',
                    default => '/ tháng',
                };
                $priceFormatted = !empty($rawPrice) ? (number_format($rawPrice, 0, ',', '.') . ' đ ' . $unitLabel) : 'Thỏa thuận';

                $pFloor = $p->floor ?? $p->floor_number;
                $floorText = ($pFloor !== null && $pFloor !== '') 
                    ? ((int)$pFloor === 0 ? 'Tầng trệt' : ('Tầng ' . $pFloor))
                    : '';

                $roomCode = $p->room_number ?? ('#' . strtoupper(substr($pId, -5)));
                $roomArea = (float)($p->area ?? 0);

                $roomAmenities = is_array($p->amenities ?? null) 
                    ? $p->amenities 
                    : (is_string($p->amenities ?? null) ? json_decode($p->amenities, true) : []);
                $roomAmenities = is_array($roomAmenities) ? array_values(array_filter($roomAmenities)) : [];

                $modalPayload = [
                    'id' => $pId,
                    'title' => $p->title ?? 'Phòng cho thuê',
                    'room_code' => $roomCode,
                    'area' => $roomArea . ' m²',
                    'floor' => $floorText ?: 'Chưa rõ tầng',
                    'price_formatted' => $priceFormatted,
                    'price' => $priceFormatted,
                    'deposit' => !empty($p->deposit) ? (number_format($p->deposit, 0, ',', '.') . ' VNĐ') : 'Thỏa thuận',
                    'payment_requirement' => $p->payment_requirement ?? null,
                    'bedroom' => (int)($p->bedroom ?? ($p->bedrooms ?? 1)),
                    'bathroom' => (int)($p->bathroom ?? ($p->bathrooms ?? 1)),
                    'is_land' => in_array($rawBType, ['dat_phan_lo', 'dat_nen']),
                    'desc' => $p->description ?? 'Không có mô tả chi tiết.',
                    'room_amenities' => $roomAmenities,
                    'created_time' => $p->created_at ? $p->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                    'status' => 'pending',
                    'status_label' => 'Chờ phê duyệt',
                    'reject_reason' => $p->reject_reason ?? '',
                    'staff_name' => $staffName,
                    'staff_contact' => $staffContact,
                    'images' => $postImages,
                    'building_name' => $bldName,
                    'building_type' => $bldType,
                    'building_type_raw' => $rawBType,
                    'building_address' => $bldAddress,
                    'no_service_pricing' => $noServicePricing,
                    'electricity_price' => $bElec,
                    'water_price' => $bWater,
                    'water_calc_type' => $bWaterType,
                    'service_fee' => $bService,
                    'internet_fee' => $bInternet,
                    'parking_fee' => $bParking,
                    'building_amenities' => $bAmenities,
                    'building_images' => $buildingImages,
                ];

                return [
                    'id' => $pId,
                    'title' => $p->title ?? 'Bài đăng phòng mới',
                    'room_code' => $roomCode,
                    'area' => $roomArea,
                    'floor_text' => $floorText,
                    'thumb_url' => $thumbUrl,
                    'images_count' => count($postImages),
                    'building_name' => $bldName,
                    'building_address' => $bldAddress,
                    'building_type' => $bldType,
                    'raw_btype' => $rawBType,
                    'author_name' => $staffName,
                    'author_phone' => $staffContact,
                    'author_initial' => $staffInitial,
                    'price' => $rawPrice,
                    'price_formatted' => $priceFormatted,
                    'created_at_formatted' => $p->created_at ? $p->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                    'modal_payload' => $modalPayload,
                    'raw_payload' => $modalPayload,
                ];
            });

            $roleData['pending_posts_count'] = $pendingPostsCount;
            $roleData['new_posts'] = $formattedPosts;

            // Lấy danh sách các bài vừa được chuyển trạng thái (approved hoặc rejected) sau $sinceTime
            $recentProcessed = Post::whereIn('status', ['approved', 'rejected'])
                ->where('updated_at', '>', $sinceTime)
                ->select(['_id', 'status', 'title'])
                ->get();

            $processedList = $recentProcessed->map(function($p) {
                $s = strtolower(trim((string)$p->status));
                return [
                    'id' => (string)($p->_id ?? $p->id),
                    'status' => in_array($s, ['approved', 'active', 'da_duyet']) ? 'approved' : 'rejected',
                    'title' => $p->title ?? 'Bài đăng',
                ];
            });
            $roleData['processed_posts'] = $processedList;
        }

        // 2.2 CHĂM SÓC KHÁCH HÀNG (CSKH): KHIẾU NẠI & PHẢN ÁNH MỚI
        if ($user->isCskh() || in_array($user->role, ['cskh', 'cham sóc kh', 'cham_soc_kh'])) {
            $pendingComplaintsCount = Complaint::where('status', '!=', 'resolved')
                ->where('status', '!=', 'escalated')
                ->where('is_escalated', '!=', true)
                ->count()
                + SupportTicket::where('type', 'khieu_nai')
                ->where('status', '!=', 'resolved')
                ->where('status', '!=', 'escalated')
                ->where('is_escalated', '!=', true)
                ->count();

            $totalComplaintsCount = Complaint::count() + SupportTicket::where('type', 'khieu_nai')->count();

            $newComplaints1 = Complaint::where('created_at', '>', $sinceTime)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            $newComplaints2 = SupportTicket::where('type', 'khieu_nai')
                ->where('created_at', '>', $sinceTime)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            $allNew = $newComplaints1->concat($newComplaints2)->sortByDesc(function ($item) {
                return $item->created_at ? $item->created_at->timestamp : 0;
            })->values();

            $formattedComplaints = $allNew->map(function ($c) {
                $cId = (string)($c->_id ?? $c->id);
                $code = $c->complaint_code ?? ($c->ticket_code ?? ('#KN-' . strtoupper(substr($cId, -5))));
                if (!str_starts_with($code, '#')) {
                    $code = '#' . $code;
                }

                $post = $c->post ?? null;
                if (!$post && !empty($c->post_id)) {
                    $post = Post::find($c->post_id);
                }
                $postTitle = $post ? ($post->title ?? 'Bài đăng trên hệ thống') : ($c->post_title ?? 'Bài đăng trên hệ thống');
                $postId = (string)($c->post_id ?? ($c->target_id ?? ($post ? ($post->_id ?? $post->id) : '')));
                $postUrl = !empty($postId) ? url('/chitietbaidang/' . $postId) : null;
                $bld = $post ? ($post->building ?? null) : null;
                $buildingName = $c->building_name ?? ($bld ? $bld->name : null);

                // Sender
                $sender = $c->user ?? null;
                if (!$sender && !empty($c->user_id)) {
                    $sender = User::find($c->user_id);
                }
                $senderName = $c->sender_name ?? ($c->name ?? ($c->customer_name ?? ($sender ? ($sender->display_name ?: ($sender->account_name ?: ($sender->username ?: 'Khách thuê'))) : 'Khách thuê')));
                $senderPhone = $c->sender_phone ?? ($c->phone ?? ($sender ? ($sender->phone ?? '---') : '---'));

                $reason = $c->reason ?? ($c->subject ?? 'Phản ánh thông tin');
                $content = $c->content ?? ($c->description ?? ($c->message ?? 'Không có mô tả chi tiết.'));

                // Images
                $rawImages = $c->images;
                if (!is_array($rawImages)) {
                    $rawImages = !empty($rawImages) ? [$rawImages] : (!empty($c->image) ? [$c->image] : []);
                }
                $imagesList = [];
                foreach ($rawImages as $imgItem) {
                    if (empty($imgItem)) continue;
                    $imgStr = (string)$imgItem;
                    if (str_starts_with($imgStr, 'http://') || str_starts_with($imgStr, 'https://') || str_starts_with($imgStr, 'data:')) {
                        $imagesList[] = $imgStr;
                    } elseif (str_starts_with($imgStr, '/storage/') || str_starts_with($imgStr, 'storage/')) {
                        $imagesList[] = asset(ltrim($imgStr, '/'));
                    } else {
                        $imagesList[] = asset('storage/' . ltrim($imgStr, '/'));
                    }
                }

                return [
                    'id' => $cId,
                    'code' => $code,
                    'title' => $reason,
                    'reason' => $reason,
                    'content' => $content,
                    'sender_name' => $senderName,
                    'sender_phone' => $senderPhone,
                    'post_id' => $postId,
                    'post_title' => $postTitle,
                    'post_url' => $postUrl,
                    'building_name' => $buildingName,
                    'images' => $imagesList,
                    'first_image' => !empty($imagesList) ? $imagesList[0] : null,
                    'more_count' => count($imagesList) > 1 ? (count($imagesList) - 1) : 0,
                    'created_at_formatted' => $c->created_at ? Carbon::parse($c->created_at)->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                    'status' => $c->status ?? 'pending'
                ];
            });

            $roleData['pending_complaints_count'] = $pendingComplaintsCount;
            $roleData['total_complaints_count'] = $totalComplaintsCount;
            $roleData['new_complaints'] = $formattedComplaints;
        }

        // 2.3 QUẢN TRỊ VIÊN (ADMIN)
        if ($user->isAdmin() || $user->role === 'admin') {
            $roleData['pending_posts_count'] = Post::whereIn('status', ['pending', 'cho_duyet', 'waiting'])->count();
            $roleData['pending_complaints_count'] = SupportTicket::where('type', 'khieu_nai')
                ->whereNotIn('status', ['resolved', 'rejected', 'da_giai_quyet'])
                ->count();
        }

        return response()->json([
            'success' => true,
            'server_time' => $serverTime,
            'unread_notifications' => $unreadCount,
            'new_notifications' => $formattedNotifs,
            'role_data' => $roleData
        ]);
    }
}

