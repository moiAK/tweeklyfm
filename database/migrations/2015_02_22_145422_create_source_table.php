<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourceTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('network_id')->unsigned();
            $table->foreign('network_id')->references('id')->on('networks');
            $table->enum('status', ['active', 'suspended', 'deleted', 'payment'])->default("active");
            $table->string('network_name');
            $table->timestamps();
            $table->dateTime("checked_at");
            $table->string("oauth_token");
            $table->string("oauth_token_secret")->null();
            $table->string("external_name")->null();
            $table->string("external_username")->null();
            $table->string("external_user_id")->null();
            $table->string("external_avatar")->null();
            $table->text('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sources');
    }

}
