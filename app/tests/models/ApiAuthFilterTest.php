<?php
 
use Mockery as m;

class ApiAuthFilterTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		$this->mocks = $this->getMocks();
		$this->filter = $this->getFilter($this->mocks);
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testIfNoKeyProvidedThrowsAuthenticationException()
	{
		Request::shouldReceive('getUser')->once()->andReturn(null);
		$this->filter->filter();
	}

	/**
	 * @expectedException AuthenticationException
	 */
	public function testIfNoUserCanBeFoundForTokenThrowsAuthenticationException()
	{
		Request::shouldReceive('getUser')->once()->andReturn('token');
		$this->mocks['repo']->shouldReceive('getUserByToken')->once()->with('token')->andReturn(null);
		$this->filter->filter();
	}

	public function testIfUserFoundLogHimIn()
	{
		Request::shouldReceive('getUser')->once()->andReturn('token');
		$this->mocks['repo']->shouldReceive('getUserByToken')->once()->with('token')->andReturn($user = m::mock('User'));
		Auth::shouldReceive('login')->once()->with($user);
		$this->filter->filter();
	}

	private function getMocks()
	{
		return array(
			'repo' => m::mock('UserRepositoryInterface'),
			);
	}

	private function getFilter(Array $mocks)
	{
		return new ApiAuthFilter($mocks['repo']);
	}
}