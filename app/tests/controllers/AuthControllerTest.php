<?php
 
use Mockery as m;

class AuthControllerTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		$this->mocks = $this->getMocks();
		$this->controller = $this->getController($this->mocks);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testAuthWithValidDataReturnsToken()
	{
		Input::shouldReceive('all')->once()->andReturn(
			$credentials = array(
				'email'    => 'c.desneuf@gmail.com',
				'password' => 'password',
				)
			);
		$this->mocks['userRepo']->shouldReceive('getUserByCredentials')->once()->with($credentials)->andReturn($user = m::mock('User'));
		$user->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);
	
		Input::shouldReceive('input')->once()->with('password', "")->andReturn('password'
			);

		$user->shouldReceive('getAttribute')->once()->with('password')->andReturn('hashedPassword');

		$this->mocks['hasher']->shouldReceive('check')->with('password', 'hashedPassword')->andReturn(true);

		$this->mocks['tokenRepo']->shouldReceive('store')->once();

		$response = $this->controller->getToken();
		$this->assertInstanceOf('Token', $response);
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testAuthWithUnvalidUsernameThrowsAuthenticationException()
	{
		Input::shouldReceive('all')->once()->andReturn(
			$credentials = array(
				'email'    => 'c.desneuf@gmail.com',
				'password' => 'password',
				)
			);
		$this->mocks['userRepo']->shouldReceive('getUserByCredentials')->once()->with($credentials)->andReturn(null);
		
		$this->controller->getToken();
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testAuthWithUnvalidPasswordThrowsAuthenticationException()
	{
		Input::shouldReceive('all')->once()->andReturn(
			$credentials = array(
				'email'    => 'c.desneuf@gmail.com',
				'password' => 'wrongPassword',
				)
			);
		$this->mocks['userRepo']->shouldReceive('getUserByCredentials')->once()->with($credentials)->andReturn($user = m::mock('User'));

		Input::shouldReceive('input')->once()->with('password', "")->andReturn('password'
			);

		$user->shouldReceive('getAttribute')->once()->with('password')->andReturn('hashedPassword');

		$this->mocks['hasher']->shouldReceive('check')->with('password', 'hashedPassword')->andReturn(false);
		
		$this->controller->getToken();
	}

	private function getMocks()
	{
		return array(
			'tokenFactory' => m::mock(new TokenFactory(1)),
			'userRepo'     => m::mock('UserRepositoryInterface'),
			'tokenRepo'    => m::mock('TokenRepositoryInterface'),
			'hasher'	   => m::mock('Illuminate\Hashing\HasherInterface'),
			);
	}

	private function getController($mocks)
	{
		return new AuthController($mocks['tokenFactory'], $mocks['tokenRepo'], $mocks['userRepo'], $mocks['hasher']);
	}
}