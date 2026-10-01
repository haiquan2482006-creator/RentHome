<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RentHomeAiService;

$service = new RentHomeAiService();

echo "=== TEST 1: Initial posting prompt does NOT default to phòng trọ ===\n";
session()->forget('ai_auto_posting_draft');
$resp1 = $service->ask('Hãy giúp tôi đăng bài');
$draft = session()->get('ai_auto_posting_draft');
assert(empty($draft['property_type']), "Draft property_type must be empty/null initially");
assert(str_contains($resp1['reply'], 'Loại hình BĐS'), "Must ask for Loại hình BĐS");
assert(str_contains($resp1['reply'], 'Căn hộ / Chung cư'), "Must contain quick button for Căn hộ / Chung cư");
assert(str_contains($resp1['reply'], 'Nhà nguyên căn'), "Must contain quick button for Nhà nguyên căn");
assert(str_contains($resp1['reply'], 'Phòng trọ'), "Must contain quick button for Phòng trọ");
assert(str_contains($resp1['reply'], 'Mặt bằng kinh doanh'), "Must contain quick button for Mặt bằng");
echo "✓ Passed: Initial prompt does NOT default to phòng trọ and includes type buttons\n";

echo "\n=== TEST 2: Providing info with Căn hộ sets property_type to 'ch' ===\n";
session()->forget('ai_auto_posting_draft');
$resp2 = $service->ask('Cho thuê căn hộ cao cấp Cầu Giấy giá 6 triệu diện tích 50m2');
$draft2 = session()->get('ai_auto_posting_draft');
assert($draft2['property_type'] === 'ch', "Property type must be 'ch'");
assert($draft2['property_type_name'] === 'Căn hộ / Chung cư', "Property type name must be Căn hộ / Chung cư");
assert(str_contains($resp2['reply'], 'Căn hộ / Chung cư'), "Summary table must display Căn hộ / Chung cư");
assert(str_contains($resp2['reply'], '🚀 Đăng bài ngay'), "Must include button 🚀 Đăng bài ngay");
echo "✓ Passed: Correctly identified as Căn hộ / Chung cư\n";

echo "\n=== TEST 3: Providing info with Nhà nguyên căn sets property_type to 'nnc' ===\n";
session()->forget('ai_auto_posting_draft');
$resp3 = $service->ask('Cho thuê nhà nguyên căn 3 tầng tại Đống Đa giá 12 triệu diện tích 80m2');
$draft3 = session()->get('ai_auto_posting_draft');
assert($draft3['property_type'] === 'nnc', "Property type must be 'nnc'");
assert($draft3['property_type_name'] === 'Nhà nguyên căn', "Property type name must be Nhà nguyên căn");
assert(str_contains($resp3['reply'], 'Nhà nguyên căn'), "Summary table must display Nhà nguyên căn");
assert(str_contains($resp3['reply'], '🚀 Đăng bài ngay'), "Must include button 🚀 Đăng bài ngay");
echo "✓ Passed: Correctly identified as Nhà nguyên căn\n";

echo "\n=== TEST 4: Providing info WITHOUT property type does NOT default to nhà trọ ===\n";
session()->forget('ai_auto_posting_draft');
$resp4 = $service->ask('Cho thuê chỗ ở Cầu Giấy giá 4 triệu diện tích 30m2');
$draft4 = session()->get('ai_auto_posting_draft');
assert(empty($draft4['property_type']), "Must NOT default to phòng trọ when not specified");
assert(str_contains($resp4['reply'], 'Chưa chọn'), "Summary table must say Chưa chọn");
assert(str_contains($resp4['reply'], 'Loại hình BĐS'), "Must list Loại hình BĐS in missing fields");
assert(str_contains($resp4['reply'], '🚀 Đăng bài ngay'), "Button 🚀 Đăng bài ngay must ALWAYS be present");
assert(str_contains($resp4['reply'], 'Loại hình: Căn hộ / Chung cư'), "Must show quick button for Căn hộ");
echo "✓ Passed: Info without property type shows 'Chưa chọn' and quick selection buttons\n";

echo "\n=== TEST 5: Clicking quick button to select property type updates draft ===\n";
$resp5 = $service->ask('Loại hình: Căn hộ / Chung cư');
$draft5 = session()->get('ai_auto_posting_draft');
assert($draft5['property_type'] === 'ch', "Draft must be updated to 'ch'");
assert(str_contains($resp5['reply'], 'Căn hộ / Chung cư'), "Summary table now displays Căn hộ / Chung cư");
assert(str_contains($resp5['reply'], '🚀 Đăng bài ngay'), "Button 🚀 Đăng bài ngay is present");
echo "✓ Passed: Clicking type button updates the draft smoothly\n";

echo "\n=== TEST 6: Incomplete draft still shows 🚀 Đăng bài ngay button ===\n";
session()->forget('ai_auto_posting_draft');
$resp6 = $service->ask('Tôi có phòng cho thuê ở Cầu Giấy');
assert(str_contains($resp6['reply'], '🚀 Đăng bài ngay'), "Button 🚀 Đăng bài ngay MUST be present even if incomplete");
assert(str_contains($resp6['reply'], '✏️ Sửa thông tin'), "Sửa thông tin button present");
assert(str_contains($resp6['reply'], '❌ Hủy đăng bài'), "Hủy đăng bài button present");
echo "✓ Passed: Incomplete draft shows 🚀 Đăng bài ngay button\n";

echo "\n=== ALL TESTS PASSED WITH 100% SUCCESS! ===\n";
