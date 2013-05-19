<?php
 
use Mockery as m;

class UsersControllerTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
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