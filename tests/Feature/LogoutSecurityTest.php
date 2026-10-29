<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogoutSecurityTest extends TestCase
{
    /**
     * Test that all critical management & admin routes are protected against unauthenticated access.
     */
    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $protectedGetRoutes = [
            '/Admin',
            '/admin',
            '/Admin/tai-chinh/duyet-chuyen-khoan',
            '/Admin/tai-chinh/cong-no-nhan-vien',
            '/Admin/tai-chinh/duyet-hoan-coc',
            '/sua_giao_dien',
            '/nha_dang_thue',
            '/nha-dang-thue',
            '/quan-ly-van-hanh',
            '/qlvan_hanh/Room_list',
            '/qlvan_hanh/tao_hop_dong',
            '/tao-hop-dong',
            '/qlvan_hanh/tao_hoa_don',
            '/tao-hoa-don',
            '/qlvan_hanh/thanh_ly_cong_no',
            '/thanh-ly-cong-no',
            '/trangcanhanuser',
            '/doi-mat-khau',
            '/Overview',
            '/dangbai',
            '/Admin/khach-hang-lien-he',
            '/Admin/tai-khoan-ca-nhan',
            '/Admin/bai-dang-noi-bat',
            '/Admin/tai-khoan-con',
            '/qltai_khoan_con',
            '/Admin/toa-nha',
            '/qltoa_nha',
            '/Admin/lich-su',
            '/lich_su',
            '/Admin/thong-bao',
            '/thong_bao',
        ];

        foreach ($protectedGetRoutes as $url) {
            $response = $this->get($url);
            $this->assertTrue(
                $response->isRedirect('/login') || $response->isRedirect(route('login')),
                "Route [{$url}] must redirect to login when unauthenticated. Received status: " . $response->getStatusCode()
            );
        }

        // Test POST operations
        $responsePostContracts = $this->post('/qlvan_hanh/contracts');
        $this->assertTrue(
            $responsePostContracts->isRedirect('/login') || $responsePostContracts->isRedirect(route('login')),
            "POST /qlvan_hanh/contracts must redirect to login when unauthenticated."
        );
    }

    /**
     * Test that API routes reject unauthenticated requests with 401 or redirect.
     */
    public function test_unauthenticated_api_requests_are_rejected(): void
    {
        $apiRoutes = [
            ['method' => 'get', 'url' => '/api/sub-accounts'],
            ['method' => 'post', 'url' => '/api/posts'],
            ['method' => 'get', 'url' => '/api/buildings'],
            ['method' => 'post', 'url' => '/api/buildings'],
            ['method' => 'get', 'url' => '/api/user/posts/stats'],
            ['method' => 'get', 'url' => '/api/notifications'],
            ['method' => 'post', 'url' => '/api/customer-accounts/dummy-id/toggle-lock'],
        ];

        foreach ($apiRoutes as $route) {
            $method = $route['method'];
            $url = $route['url'];
            $response = $this->json(strtoupper($method), $url);
            
            $this->assertTrue(
                in_array($response->getStatusCode(), [401, 403, 302]),
                "API Route [{$method} {$url}] should return 401/403/302 for unauthenticated requests. Received status: " . $response->getStatusCode()
            );
        }
    }

    /**
     * Test that logging out clears session and prevents reusing copied URLs.
     */
    public function test_logging_out_invalidates_session_and_clears_auth(): void
    {
        // 1. Create a dummy test user in memory
        $user = new User([
            'username' => 'test_admin_user',
            'role' => 'admin',
            'status' => 'active'
        ]);

        // 2. Act as authenticated user
        $this->actingAs($user);
        $this->assertTrue(Auth::check(), 'User should be authenticated.');

        // 3. User performs logout
        $logoutResponse = $this->post(route('logout'));
        
        // Assert redirect to login or home
        $this->assertTrue($logoutResponse->isRedirect());
        
        // Assert no-cache / no-store headers are set
        $this->assertStringContainsString('no-store', (string)$logoutResponse->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', (string)$logoutResponse->headers->get('Cache-Control'));

        // 4. In a new request (simulating opening a new tab and pasting the URL)
        $newTabResponse = $this->get('/Admin');
        $this->assertTrue(
            $newTabResponse->isRedirect('/login') || $newTabResponse->isRedirect(route('login')),
            'Accessing /Admin after logout must redirect to login.'
        );
        $this->assertFalse(Auth::check(), 'User must NOT be authenticated after logout.');
    }

    /**
     * Test that PreventBackHistory middleware sets security headers on all web responses.
     */
    public function test_prevent_back_history_headers_are_applied(): void
    {
        $response = $this->get('/login');
        $this->assertEquals(200, $response->getStatusCode());
        
        $cacheControl = (string)$response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }
}
