<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('user_id');
            $table->mediumText('image');
            // $table->unsignedBigInteger('friend_id');
            // $table->foreign('friend_id')->references('id')->on('users')->onDelete('cascade');
            // $table->enum('status', array('going', 'pending invite'));
            $table->text('description');
            $table->text('start_date');
            $table->text('name');
            $table->text('end_date');
            $table->text('start_time');
            $table->text('end_time');
            $table->string('location');
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
        Schema::dropIfExists('events');
    }
}
