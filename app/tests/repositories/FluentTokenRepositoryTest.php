<?php
 
use Mockery as m;

class FluentTokenRepositoryTest extends TestCase {

	public function setUp()
	{
		$this->mocks = $this->getMocks();
		$this->repo = $this->getRepo($this->mocks);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testStoreStoresToken()
	{
		$user = m::mock('User');
		$token = m::mock('Token');
		$token->shouldReceive('getId')->once()->andReturn('tokenId');
		$token->shouldReceive('getUserId')->once()->andReturn(1);
		$token->shouldReceive('getExpiration')->once()->andReturn(12345);

		$attributes = array(
			'id'         => 'tokenId',
			'user_id'    => 1,
			'expiration' => 12345,
			);

		$this->mocks['db']->shouldReceive('table')->once()->with('tokens')->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('insert')->once()->with($attributes);
		$this->repo->store($token);
	}

	public function testDeleteExpiredDeleteAllExpiredTokens()
	{
		$this->mocks['db']->shouldReceive('table')->once()->with('tokens')->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('where')->once()->with('expiration', '<', 'DateTime')->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('delete')->once();
		$this->repo->deleteExpired();
	}

	public function testDeleteAllDeleteAllTokens()
	{
		$this->mocks['db']->shouldReceive('table')->once()->with('tokens')->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('truncate')->once();
		$this->repo->deleteAll();
	}

	public function testGetForUserReturnsTokenIfExists()
	{
		$this->mocks['db']->shouldReceive('table')->once()->with('tokens')->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('where')->once()->with('user_id', '=', 1)->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('where')->once()->with('expiration', '>', 'DateTime')->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('first')->once()->andReturn(m::mock('Token'));
		$user = m::mock('User');
		$user->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);
		$result = $this->repo->getForUser($user);
		$this->assertInstanceOf('Token', $result);
	}

	private function getMocks()
	{
		return array(
			'db' => m::mock('Illuminate\Database\DatabaseManager'),
			);
	}

	private function getRepo($mocks)
	{
		return new FluentTokenRepository($mocks['db']);
	}

}