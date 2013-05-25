<?php

class UserSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$user = new User;
		$user->email = 'c.desneuf@gmail.com';
		$user->password = 'password';
		$user->save();
	}

}