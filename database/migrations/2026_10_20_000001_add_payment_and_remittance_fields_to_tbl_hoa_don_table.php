<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Kiểm tra và tạo/cập nhật bảng tbl_hoa_don
        if (!Schema::hasTable('tbl_hoa_don')) {
            Schema::create('tbl_hoa_don', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_code')->nullable()->index();
                $table->string('contract_id')->nullable()->index();
                $table->string('room_id')->nullable();
                $table->string('post_id')->nullable();
                $table->string('tenant_name')->nullable();
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->string('status')->default('unpaid'); // unpaid, paid, pending_approval
                $table->string('payment_status')->default('unpaid');
                
                // Các trường phục vụ luồng Tài chính & Công nợ
                $table->string('payment_method')->nullable(); // bank_transfer / cash
                $table->string('proof_image')->nullable();    // Lưu ảnh bill khách upload
                $table->unsignedBigInteger('collected_by')->nullable()->index(); // ID nhân sự thu tiền mặt
                $table->boolean('is_remitted_to_admin')->default(false);        // Đã nộp tiền về quỹ công ty chưa
                
                $table->timestamp('transfer_reported_at')->nullable(); // Thời gian khách báo chuyển khoản
                $table->timestamp('payment_date')->nullable();
                $table->timestamp('remitted_at')->nullable();          // Thời gian nộp tiền cho Admin
                $table->unsignedBigInteger('admin_approved_by')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('tbl_hoa_don', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_hoa_don', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('payment_status'); // bank_transfer / cash
                }
                if (!Schema::hasColumn('tbl_hoa_don', 'proof_image')) {
                    $table->string('proof_image')->nullable()->after('payment_method'); // Ảnh bill chuyển khoản
                }
                if (!Schema::hasColumn('tbl_hoa_don', 'collected_by')) {
                    $table->unsignedBigInteger('collected_by')->nullable()->after('proof_image'); // Nhân viên thu tiền
                }
                if (!Schema::hasColumn('tbl_hoa_don', 'is_remitted_to_admin')) {
                    $table->boolean('is_remitted_to_admin')->default(false)->after('collected_by'); // Trạng thái nộp về công ty
                }
                if (!Schema::hasColumn('tbl_hoa_don', 'transfer_reported_at')) {
                    $table->timestamp('transfer_reported_at')->nullable()->after('is_remitted_to_admin');
                }
                if (!Schema::hasColumn('tbl_hoa_don', 'remitted_at')) {
                    $table->timestamp('remitted_at')->nullable()->after('transfer_reported_at');
                }
            });
        }

        // 2. Bổ sung đồng thời cho bảng 'invoices' nếu có dùng trên hệ thống SQL
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'payment_method')) {
                    $table->string('payment_method')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'proof_image')) {
                    $table->string('proof_image')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'collected_by')) {
                    $table->unsignedBigInteger('collected_by')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'is_remitted_to_admin')) {
                    $table->boolean('is_remitted_to_admin')->default(false);
                }
                if (!Schema::hasColumn('invoices', 'transfer_reported_at')) {
                    $table->timestamp('transfer_reported_at')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'remitted_at')) {
                    $table->timestamp('remitted_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tbl_hoa_don')) {
            Schema::table('tbl_hoa_don', function (Blueprint $table) {
                $columns = ['payment_method', 'proof_image', 'collected_by', 'is_remitted_to_admin', 'transfer_reported_at', 'remitted_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('tbl_hoa_don', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $columns = ['payment_method', 'proof_image', 'collected_by', 'is_remitted_to_admin', 'transfer_reported_at', 'remitted_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('invoices', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
