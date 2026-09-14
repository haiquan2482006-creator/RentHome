<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ModeratorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::where('email', 'kiemduyet@renthome.vn')->delete();

        User::updateOrCreate(
            ['email' => 'kiemduyet@gmail.com'],
            [
                'username' => 'kiemduyetvien',
                'account_name' => 'Kiểm Duyệt Viên System',
                'phone' => '0987654321',
                'password' => Hash::make('123456'),
                'account_type' => 'moderator',
                'role' => 'moderator',
                'status' => 'active',
                'is_deleted' => false,
                'is_locked' => false,
            ]
        );

        $this->command->info('Đã khởi tạo/cập nhật tài khoản Kiểm duyệt viên thành công!');
    }
}
