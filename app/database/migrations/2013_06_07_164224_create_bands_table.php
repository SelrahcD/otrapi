<?php

use Illuminate\Database\Migrations\Migration;

class CreateBandsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create bands table
		Schema::create('bands', function($t)
		{
			$t->increments('id');
			$t->string('name');
			$t->timestamps();
		});


		// Create pivot table
		Schema::create('bands_users', function($t)
		{
			$t->integer('band_id');
			$t->integer('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop tables
		Schema::drop('bands');
		Schema::drop('bands_users');
	}

}