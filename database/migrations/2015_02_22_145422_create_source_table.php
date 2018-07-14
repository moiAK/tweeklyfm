<?php

/*
 * This file is part of tweeklyfm/tweeklyfm
 *
 *  (c) Scott Wilcox <scott@dor.ky>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('network_id')->unsigned();
            $table->foreign('network_id')->references('id')->on('networks');
            $table->enum('status', ['active', 'suspended', 'deleted', 'payment'])->default('active');
            $table->string('network_name');
            $table->timestamps();
            $table->dateTime('checked_at');
            $table->string('oauth_token');
            $table->string('oauth_token_secret')->null();
            $table->string('external_name')->null();
            $table->string('external_username')->null();
            $table->string('external_user_id')->null();
            $table->string('external_avatar')->null();
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
