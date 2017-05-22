<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWidthAndHeightColumnsToPhotoPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photo_posts', function (Blueprint $table) {
            $table->float('width')->nullable();
            $table->float('height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photo_posts', function (Blueprint $table) {
            $table->dropColumn(['width', 'height']);
        });
    }
}
