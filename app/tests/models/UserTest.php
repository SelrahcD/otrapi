<?php
 
use Mockery as m;

class UserTest extends TestCase {

	protected $user; 

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		$this->user = new User;
	}

	public function testPasswordIsHashedOnDb()
	{
		// IN ARDENT
	}

}