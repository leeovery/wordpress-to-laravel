<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wp_id')->unique();
            $table->string('title');
            $table->string('slug');
            $table->string('featured_image')->nullable();
            $table->boolean('sticky')->default(false);
            $table->longText('excerpt')->nullable();
            $table->longText('content');
            $table->string('format')->nullable();
            $table->string('status');
            $table->timestamp('published_at')->nullable();
            $table->integer('author_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
