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
        Schema::table('post_media', function (Blueprint $table) {
            $table->integer('width')->nullable()->change();
            $table->integer('height')->nullable()->change();
            $table->string('path')->nullable()->change();
            $table->string('disk')->default('r2')->after('type');
            $table->string('temporary_path')->nullable()->after('path');
            $table->string('poster_path')->nullable()->after('temporary_path');
            $table->string('status')->default('pending')->after('poster_path');
            $table->string('mime_type')->nullable()->after('status');
            $table->unsignedBigInteger('bytes')->nullable()->after('mime_type');
            $table->unsignedInteger('duration_seconds')->nullable()->after('bytes');
            $table->string('failure_code')->nullable()->after('duration_seconds');
            $table->text('failure_message')->nullable()->after('failure_code');
            $table->unsignedTinyInteger('sort_order')->default(0)->after('failure_message');
            $table->timestamp('processing_started_at')->nullable()->after('sort_order');
            $table->timestamp('processed_at')->nullable()->after('processing_started_at');
            $table->timestamp('failed_at')->nullable()->after('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_media', function (Blueprint $table) {
            $table->dropColumn([
                'disk',
                'temporary_path',
                'poster_path',
                'status',
                'mime_type',
                'bytes',
                'duration_seconds',
                'failure_code',
                'failure_message',
                'sort_order',
                'processing_started_at',
                'processed_at',
                'failed_at',
            ]);

            $table->integer('width')->nullable(false)->change();
            $table->integer('height')->nullable(false)->change();
            $table->string('path')->nullable(false)->change();
        });
    }
};
