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

	public function testPasswordIsHashed()
	{
		$this->user->password = 'password';
		$this->assertNotEquals('password', $this->user->password);
		$this->assertNotNull($this->user->password);
	}
}