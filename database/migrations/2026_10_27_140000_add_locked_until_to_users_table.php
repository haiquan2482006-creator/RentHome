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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'locked_until')) {
                    $table->timestamp('locked_until')->nullable()->after('is_locked');
                }
                if (!Schema::hasColumn('users', 'lock_reason')) {
                    $table->string('lock_reason')->nullable()->after('locked_until');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'locked_until')) {
                    $table->dropColumn('locked_until');
                }
                if (Schema::hasColumn('users', 'lock_reason')) {
                    $table->dropColumn('lock_reason');
                }
            });
        }
    }
};
