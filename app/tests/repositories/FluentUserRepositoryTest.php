<?php
 
use Mockery as m;

class FluentUserRepositoryTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testGetUserByCredentialsReturnsNullIfNotFound()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$mocks['db']->shouldReceive('table')->once()->with('users')->andReturn($builder = m::mock('\Illuminate\Database\Query\Builder'));
		$builder->shouldReceive('where')->once()->with('email', '=', 'c.desneuf@gmail.com')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(null);
		$user = $repo->getUserByCredentials(array('email' => 'c.desneuf@gmail.com'));
		$this->assertNull($user);
	}

	public function testGetUserByCredentialsReturnsUserIfFound()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$mocks['db']->shouldReceive('table')->once()->with('users')->andReturn($builder = m::mock('\Illuminate\Database\Query\Builder'));
		$builder->shouldReceive('where')->once()->with('email', '=', 'c.desneuf@gmail.com')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn($attributes = array());
		$result = $repo->getUserByCredentials(array('email' => 'c.desneuf@gmail.com'));
		$this->assertInstanceOf('User', $result);
	}


	private function getMocks()
	{
		return array(
			'db' => m::mock('Illuminate\Database\DatabaseManager'),
			);
	}

	private function getRepo($mocks)
	{
		return new FluentUserRepository($mocks['db']);
	}

}