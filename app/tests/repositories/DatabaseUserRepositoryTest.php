<?php
 
use Mockery as m;

class DatabaseUserRepositoryTest extends TestCase {

	public function setUp()
	{
		$this->mocks = $this->getMocks();
		$this->repo = $this->getRepo($this->mocks);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testMakeReturnsAUser()
	{
		$attributes = array('email' => 'c.desneuf@gmail.com');
		$this->mocks['model']->shouldReceive('newInstance')->once()->with($attributes)->andReturn($user = m::mock('User'));
		$result = $this->repo->make($attributes);	
		$this->assertInstanceOf('User', $result);
	}

	public function testStoreStoresUser()
	{
		$user = m::mock('User');
		$user->shouldReceive('save')->once();
		$this->repo->store($user);
	}


	public function testGetUserByCredentialsReturnsNullIfNotFound()
	{
		$this->mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('where')->once()->with('email', '=', 'c.desneuf@gmail.com')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(null);
		$this->assertNull($this->repo->getUserByCredentials(array('email' => 'c.desneuf@gmail.com')));
	}

	public function testGetUserByCredentialsReturnsUserIfFound()
	{
		$this->mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('where')->once()->with('email', '=', 'c.desneuf@gmail.com')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(m::mock('User'));
		$result = $this->repo->getUserByCredentials(array('email' => 'c.desneuf@gmail.com'));
		$this->assertInstanceOf('User', $result);
	}

	public function testGetUserByTokenReturnsUserIfFoundAndTokenIsValid()
	{
		$this->mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('join')->once()->with('tokens', 'users.id', '=', 'tokens.user_id')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('tokens.id', '=', 'token')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('expiration', '>', 'DateTime')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(m::mock('User'));
		$result = $this->repo->getUserByToken('token');
		$this->assertInstanceOf('User', $result);
	}

	public function testGetUserByTokenWithExpiredFalseReturnsUserIfFound()
	{
		$this->mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('join')->once()->with('tokens', 'users.id', '=', 'tokens.user_id')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('tokens.id', '=', 'token')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(m::mock('User'));
		$result = $this->repo->getUserByToken('token', false);
		$this->assertInstanceOf('User', $result);
	}

	public function testGetUserByTokenReturnsNullIfNotFoundOrTokenInvalid()
	{
		$this->mocks['model']->shouldReceive('newQuery')->once()->andReturn($builder = m::mock('\Illuminate\Database\Eloquent\Builder'));
		$builder->shouldReceive('join')->once()->with('tokens', 'users.id', '=', 'tokens.user_id')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('tokens.id', '=', 'token')->andReturn($builder);
		$builder->shouldReceive('where')->once()->with('expiration', '>', 'DateTime')->andReturn($builder);
		$builder->shouldReceive('first')->once()->andReturn(null);
		$result = $this->repo->getUserByToken('token');
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