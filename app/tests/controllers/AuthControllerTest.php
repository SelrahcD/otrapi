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

	/**
	 * @expectedException ErrorMessageException
	 */
	public function testAuthThrowsErrorMessageEXceptionIfEmailIsMissing()
	{
		Input::shouldReceive('has')->with('email')->andReturn(false);
		Input::shouldReceive('has')->with('password')->andReturn(true);
		$this->controller->getToken();
	}

	/**
	 * @expectedException ErrorMessageException
	 */
	public function testAuthThrowsErrorMessageEXceptionIfPasswordIsMissing()
	{
		Input::shouldReceive('has')->with('email')->andReturn(true);
		Input::shouldReceive('has')->with('password')->andReturn(false);
		$this->controller->getToken();
	}


	public function testAuthWithValidDataReturnsNewTokenIfNoValidExisting()
	{
		Input::shouldReceive('has')->with('email')->andReturn(true);
		Input::shouldReceive('has')->with('password')->andReturn(true);
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

		$this->mocks['tokenRepo']->shouldReceive('getForUser')->once()->with($user)->andReturn(null);

		$this->mocks['tokenRepo']->shouldReceive('store')->once();

		$response = $this->controller->getToken();
		$this->assertInstanceOf('Token', $response);
	}

	public function testAuthWithValidDataReturnExistingTokenIfExisting()
	{
		Input::shouldReceive('has')->with('email')->andReturn(true);
		Input::shouldReceive('has')->with('password')->andReturn(true);
		Input::shouldReceive('all')->once()->andReturn(
			$credentials = array(
				'email'    => 'c.desneuf@gmail.com',
				'password' => 'password',
				)
			);
		$this->mocks['userRepo']->shouldReceive('getUserByCredentials')->once()->with($credentials)->andReturn($user = m::mock('User'));
				
		Input::shouldReceive('input')->once()->with('password', "")->andReturn('password'
			);

		$this->mocks['hasher']->shouldReceive('check')->with('password', 'hashedPassword')->andReturn(true);

		$user->shouldReceive('getAttribute')->once()->with('password')->andReturn('hashedPassword');

		$this->mocks['tokenRepo']->shouldReceive('getForUser')->once()->with($user)->andReturn(m::mock('Token'));

		$response = $this->controller->getToken();
		$this->assertInstanceOf('Token', $response);
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testAuthWithUnvalidUsernameThrowsAuthenticationException()
	{
		Input::shouldReceive('has')->with('email')->andReturn(true);
		Input::shouldReceive('has')->with('password')->andReturn(true);
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
		Input::shouldReceive('has')->with('email')->andReturn(true);
		Input::shouldReceive('has')->with('password')->andReturn(true);
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

	/**
	 * @expectedException AuthenticationException
	 */
	public function testRefreshTokenThrowsAuthExceptionIfNotTokenProvided()
	{
		Request::shouldReceive('getUser')->once()->andReturn(null);
		$this->controller->refreshToken();
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testRefreshTokenThrowsAuthExceptionIfCantGetUser()
	{
		Request::shouldReceive('getUser')->once()->andReturn('tokenId');
		$this->mocks['userRepo']->shouldReceive('getUserByToken')->once()->with('tokenId', false)->andReturn(null);
		$this->controller->refreshToken();
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testRefreshTokenThrowsAuthExceptionIfCantGetToken()
	{
		Request::shouldReceive('getUser')->once()->andReturn('tokenId');
		$this->mocks['userRepo']->shouldReceive('getUserByToken')->once()->with('tokenId', false)->andReturn($user = m::mock('User'));
		$this->mocks['tokenRepo']->shouldReceive('getForUser')->once()->with($user, false)->andReturn(null);
		$this->controller->refreshToken();
	}

	/**
	 * @expectedException ErrorMessageException
	 */
	public function testRefreshTokenThrowsErrorMessageExceptionIfRefreshTokenDoesntMatch()
	{
		Request::shouldReceive('getUser')->once()->andReturn('tokenId');
		$this->mocks['userRepo']->shouldReceive('getUserByToken')->once()->with('tokenId', false)->andReturn($user = m::mock('User'));
		$this->mocks['tokenRepo']->shouldReceive('getForUser')->once()->with($user, false)->andReturn($token = m::mock('Token'));
		Input::shouldReceive('input')->once()->with('refresh_token', '')->andReturn('refresh');
		$token->shouldReceive('getRefresh')->once()->andReturn('NotTheSame');
		$this->controller->refreshToken();
	}

	public function testRefreshTokenDeletesOldTokenCreatesNewOneStoresItAndReturnsIt()
	{
		Request::shouldReceive('getUser')->once()->andReturn('tokenId');
		$this->mocks['userRepo']->shouldReceive('getUserByToken')->once()->with('tokenId', false)->andReturn($user = m::mock('User'));

		$this->mocks['tokenRepo']->shouldReceive('getForUser')->once()->with($user, false)->andReturn($token = m::mock('Token'));

		Input::shouldReceive('input')->once()->with('refresh_token', '')->andReturn('refresh');

		$token->shouldReceive('getRefresh')->once()->andReturn('refresh');

		$this->mocks['tokenRepo']->shouldReceive('delete')->once()->with($token);

		$this->mocks['tokenFactory']->shouldReceive('createTokenForUser')->once()->with($user)->andReturn($newToken = m::mock('Token'));

		$this->mocks['tokenRepo']->shouldReceive('store')->once()->with($newToken);

		$response = $this->controller->refreshToken();

		$this->assertInstanceOf('Token', $response);
		$this->assertEquals($response, $newToken);
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