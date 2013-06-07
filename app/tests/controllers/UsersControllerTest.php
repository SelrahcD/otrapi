<?php
 
use Mockery as m;

class UsersControllerTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		parent::setUp();

		$this->mocks = $this->getMocks();
		$this->controller = $this->getController($this->mocks);
	}

	public function testCreateCreatesANewUserIfProvidedDataAreValid()
	{
		Input::shouldReceive('all')->once()->andReturn($input = 'input');
		$this->mocks['userRepo']->shouldReceive('make')->once()->with($input)->andReturn($user = m::mock('User'));
		$user->shouldReceive('validate')->once()->andReturn(true);
		$this->mocks['userRepo']->shouldReceive('store')->once()->with($user);
		$response = $this->controller->create();

		$this->assertEquals($user, $response);
	}

	/**
	 * @expectedException ValidationException
	 */
	public function testCreateThrowsValidationExceptionIfDataIsntValid()
	{
		Input::shouldReceive('all')->once()->andReturn($input = 'input');
		$this->mocks['userRepo']->shouldReceive('make')->once()->with($input)->andReturn($user = m::mock('User'));
		$user->shouldReceive('validate')->once()->andReturn(false);
		$user->shouldReceive('errors')->once()->andReturn($errors = array());
		$this->controller->create();
	}

	public function testShowMeShowCurrentUserData()
	{
		Auth::shouldReceive('user')->once()->andReturn($user = m::mock('User'));
		$response = $this->controller->showMe();
		$this->assertEquals($user, $response);
	}

	/**
	 * @expectedException ValidationException
	 */
	public function testEditMeThrowsValidationExceptionIfDataIsntValid()
	{
		Input::shouldReceive('all')->once()->andReturn($input = array());
		Auth::shouldReceive('user')->once()->andReturn($user = m::mock('User'));
		$user->shouldReceive('fill')->once()->with($input);
		$user->shouldReceive('validate')->once()->andReturn(false);
		$user->shouldReceive('errors')->once()->andReturn($errors = array());
		$this->controller->editMe();
	}

	public function testEditMeUpdatesUserIfDataAreValid()
	{
		Input::shouldReceive('all')->once()->andReturn($input = array());
		Auth::shouldReceive('user')->once()->andReturn($user = m::mock('User'));
		$user->shouldReceive('fill')->once()->with($input);
		$user->shouldReceive('validate')->once()->andReturn(true);
		$this->mocks['userRepo']->shouldReceive('store')->once()->with($user);
		$response = $this->controller->editMe();
		$this->assertEquals($user, $response);
	}

	private function getMocks()
	{
		return array(
			'userRepo'     => Mockery::mock('UserRepositoryInterface'),
			);
	}

	private function getController($mocks)
	{
		return new UsersController($mocks['userRepo']);
	}
}