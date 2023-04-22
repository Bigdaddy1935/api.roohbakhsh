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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone',11)->unique();
            $table->string('fullname',200)->nullable();
            $table->string('username',100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',255);
            $table->string('api_token','60')->nullable();
            $table->boolean('approved')->default(0);
            $table->string('email')->unique()->nullable();
            $table->boolean('role')->default(0);
            $table->text('picture')->nullable();
            $table->boolean('gender')->default(0);
            $table->string('national_code',20)->nullable();
            $table->string('birthday',255)->nullable();
            $table->text('born_place')->nullable();
            $table->boolean('status_users')->default(0);
            $table->bigInteger('score')->default(0);
            $table->datetime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->text('address')->nullable();
            $table->longText('about_me')->nullable();
            $table->text('bio_photo')->nullable();
            $table->string('city')->nullable();
            $table->string('parent_num',20)->unique()->nullable();
            $table->string('postal',10)->unique()->nullable();
            $table->string('amount')->nullable();
            $table->string('authority')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
