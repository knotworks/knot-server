<?php

declare(strict_types=1);

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_disk')->nullable()->after('avatar');
            $table->string('avatar_temporary_path')->nullable()->after('avatar_disk');
            $table->string('avatar_status')->nullable()->after('avatar_temporary_path');
            $table->string('avatar_mime_type')->nullable()->after('avatar_status');
            $table->unsignedBigInteger('avatar_bytes')->nullable()->after('avatar_mime_type');
            $table->string('avatar_failure_code')->nullable()->after('avatar_bytes');
            $table->text('avatar_failure_message')->nullable()->after('avatar_failure_code');
            $table->timestamp('avatar_processing_started_at')->nullable()->after('avatar_failure_message');
            $table->timestamp('avatar_processed_at')->nullable()->after('avatar_processing_started_at');
            $table->timestamp('avatar_failed_at')->nullable()->after('avatar_processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_disk',
                'avatar_temporary_path',
                'avatar_status',
                'avatar_mime_type',
                'avatar_bytes',
                'avatar_failure_code',
                'avatar_failure_message',
                'avatar_processing_started_at',
                'avatar_processed_at',
                'avatar_failed_at',
            ]);
        });
    }
};
