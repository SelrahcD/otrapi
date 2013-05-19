<?php
 
use Mockery as m;

class FluentTokenRepositoryTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function testStoreStoresToken()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
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

		$mocks['db']->shouldReceive('table')->once()->with('tokens')->andReturn($mocks['db']);
		$mocks['db']->shouldReceive('insert')->once()->with($attributes);
		$repo->store($token);
	}

	public function testDeleteExpiredDeleteAllExpiredTokens()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$mocks['db']->shouldReceive('table')->once()->with('tokens')->andReturn($mocks['db']);
		$mocks['db']->shouldReceive('where')->once()->with('expiration', '<', 'DateTime')->andReturn($mocks['db']);
		$mocks['db']->shouldReceive('delete')->once();
		$repo->deleteExpired();
	}

	public function testDeleteAllDeleteAllTokens()
	{
		$mocks = $this->getMocks();
		$repo = $this->getRepo($mocks);
		$mocks['db']->shouldReceive('table')->once()->with('tokens')->andReturn($mocks['db']);
		$mocks['db']->shouldReceive('truncate')->once();
		$repo->deleteAll();
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