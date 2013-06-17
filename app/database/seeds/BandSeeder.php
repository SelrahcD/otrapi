<?php

class BandSeeder extends Seeder {
	
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$band = new Band;
		$band->name = 'band1';
		$band->save();
	}
	
}