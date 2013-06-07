<?php
 
use Mockery as m;

class DatabaseUserRepositoryTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testMakeReturnsAUser()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$attributes = array('email' => 'c.desneuf@gmail.com');
		$mocks['model']->shouldReceive('newInstance')->once()->with($attributes)->andReturn($user = m::mock('User'));
		$result = $repo->make($attributes);	
		$this->assertInstanceOf('User', $result);
	}

	public function testStoreStoresUser()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$user = m::mock('User');
		$user->shouldReceive('save')->once();
		$repo->store($user);
	}


	public function testGetUserByCredentialsReturnsNullIfNotFound()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('where')->once()->with('email', '=', 'c.desneuf@gmail.com')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(null);
		$this->assertNull($repo->getUserByCredentials(array('email' => 'c.desneuf@gmail.com')));
	}

	public function testGetUserByCredentialsReturnsUserIfFound()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('where')->once()->with('email', '=', 'c.desneuf@gmail.com')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(m::mock('User'));
		$result = $repo->getUserByCredentials(array('email' => 'c.desneuf@gmail.com'));
		$this->assertInstanceOf('User', $result);
	}

	public function testGetUserByTokenReturnsUserIfFoundAndTokenIsValid()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('join')->once()->with('tokens', 'users.id', '=', 'tokens.user_id')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('tokens.id', '=', 'token')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('expiration', '>', 'DateTime')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(m::mock('User'));
		$result = $repo->getUserByToken('token');
		$this->assertInstanceOf('User', $result);
	}

	public function testGetUserByTokenReturnsUserIfNotFoundOrTokenInvalid()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('join')->once()->with('tokens', 'users.id', '=', 'tokens.user_id')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('tokens.id', '=', 'token')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('expiration', '>', 'DateTime')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(null);
		$result = $repo->getUserByToken('token');
		$this->assertNull($result);
	}

	private function getMocks()
	{
		return array(
			'model' => m::mock('User'),
			);
	}

	private function getRepo($mocks)
	{
		return new DatabaseUserRepository($mocks['model']);
	}
}