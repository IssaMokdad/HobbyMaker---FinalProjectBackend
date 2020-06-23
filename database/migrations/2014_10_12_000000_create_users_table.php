<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('avatar')->default('avatar.png');
            $table->enum('gender', array('male', 'female'));
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('activation_token');
            $table->boolean('active')->default(false);
            $table->softDeletes();
            $table->string('password');
            $table->date('birthday');
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
}
