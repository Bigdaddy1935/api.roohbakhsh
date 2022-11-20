<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_user_id');
            $table->string('course_title', 100)->unique();
            $table->text('description')->nullable();
            $table->string('free')->default(0);
            $table->tinyInteger('course_visibility')->default(0);
            $table->string('code', 100)->default('')->nullable();
            $table->boolean('access')->default(0);
            $table->string('course_teacher');
            $table->boolean('course_status')->default(true);
            $table->boolean('navigation')->default(true);
            $table->text('picture')->nullable();
            $table->foreign('course_user_id')->references('id')->on('users')->cascadeOnUpdate();
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
        Schema::dropIfExists('courses');
    }
};
