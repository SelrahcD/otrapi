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
		$user->email = 'user1@test.fr';
		$user->password = 'password';
		$user->save();

		$user = new User;
		$user->email = 'user2@test.fr';
		$user->password = 'password';
		$user->save();

		$user = new User;
		$user->email = 'user3@test.fr';
		$user->password = 'password';
		$user->save();
	}

}