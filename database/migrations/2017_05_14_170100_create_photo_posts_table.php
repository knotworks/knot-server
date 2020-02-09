<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotoPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->text('body')->nullable();
            $table->string('image_path');
            $table->unsignedInteger('user_id')->index();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->boolean('cloud')->default(false);
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photo_posts');
    }
}
