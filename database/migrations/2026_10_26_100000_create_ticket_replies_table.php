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
        Schema::connection('mongodb')->create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->index();
            $table->string('ticket_type')->default('complaint');
            $table->string('sender_id')->nullable()->index();
            $table->string('sender_name')->nullable();
            $table->string('sender_avatar')->nullable();
            $table->boolean('is_cskh')->default(true);
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->dropIfExists('ticket_replies');
    }
};
