<?php
 
use Mockery as m;

class AuthControllerTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testAuthWithValidDataReturnsToken()
	{
		$mocks = $this->getMocks();
		$controller = $this->getController($mocks);
		Input::shouldReceive('all')->once()->andReturn(
			$credentials = array(
				'email'    => 'c.desneuf@gmail.com',
				'password' => 'password',
				)
			);
		$mocks['userRepo']->shouldReceive('getUserByCredentials')->once()->with($credentials)->andReturn($user = m::mock('User'));
		$user->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);
	
		Input::shouldReceive('input')->once()->with('password', "")->andReturn('password'
			);

		$user->shouldReceive('getAttribute')->once()->with('password')->andReturn('hashedPassword');

		$mocks['hasher']->shouldReceive('check')->with('password', 'hashedPassword')->andReturn(true);

		$mocks['tokenRepo']->shouldReceive('store')->once();

		$response = $controller->getToken();
		$this->assertInstanceOf('Token', $response);
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testAuthWithUnvalidUsernameThrowsAuthenticationException()
	{
		$mocks = $this->getMocks();
		$controller = $this->getController($mocks);
		Input::shouldReceive('all')->once()->andReturn(
			$credentials = array(
				'email'    => 'c.desneuf@gmail.com',
				'password' => 'password',
				)
			);
		$mocks['userRepo']->shouldReceive('getUserByCredentials')->once()->with($credentials)->andReturn(null);
		
		$controller->getToken();
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testAuthWithUnvalidPasswordThrowsAuthenticationException()
	{
		$mocks = $this->getMocks();
		$controller = $this->getController($mocks);
		Input::shouldReceive('all')->once()->andReturn(
			$credentials = array(
				'email'    => 'c.desneuf@gmail.com',
				'password' => 'wrongPassword',
				)
			);
		$mocks['userRepo']->shouldReceive('getUserByCredentials')->once()->with($credentials)->andReturn($user = m::mock('User'));

		Input::shouldReceive('input')->once()->with('password', "")->andReturn('password'
			);

		$user->shouldReceive('getAttribute')->once()->with('password')->andReturn('hashedPassword');

		$mocks['hasher']->shouldReceive('check')->with('password', 'hashedPassword')->andReturn(false);
		
		$controller->getToken();
	}

	private function getMocks()
	{
		return array(
			'tokenFactory' => Mockery::mock(new TokenFactory(1)),
			'userRepo'     => Mockery::mock('UserRepositoryInterface'),
			'tokenRepo'    => Mockery::mock('TokenRepositoryInterface'),
			'hasher'	   => Mockery::mock('Illuminate\Hashing\HasherInterface'),
			);
	}

	private function getController($mocks)
	{
		return new AuthController($mocks['tokenFactory'], $mocks['tokenRepo'], $mocks['userRepo'], $mocks['hasher']);
	}
}