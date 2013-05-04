<?php

use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create tokens table
		Schema::create('tokens', function($t)
		{
			$t->string('id');
			$t->integer('user_id');
			$t->timestamp('expiration');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop tokens table
		Schema::drop('tokens');
	}

}