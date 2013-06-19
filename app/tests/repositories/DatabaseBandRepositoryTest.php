<?php

use Mockery as m;

class DatabaseBandRepositoryTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		$this->mocks = $this->getMocks();
		$this->repo = $this->getRepo($this->mocks);
	}

	public function testMakeReturnsABand()
	{
		$attributes = array('name' => 'Taskane');
		$this->mocks['model']->shouldReceive('newInstance')->once()->with($attributes)->andReturn($band = m::mock('Band'));
		$result = $this->repo->make($attributes);
		$this->assertInstanceOf('Band', $result);
	}

	public function testStoreStoresBand()
	{
		$band = m::mock('Band');
		$band->shouldReceive('save')->once();
		$this->repo->store($band);
	}

	public function testIsUserAMemberReturnsTrueIfUserAMember()
	{
		$band = m::mock('Band');
		$user = m::mock('User');
		$band->shouldReceive('getAttribute')->once()->andReturn(1);
		$user->shouldReceive('getAttribute')->once()->andReturn(1);
		$this->mocks['db']->shouldReceive('table')->once()->with('band_user')->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('where')->once()->with('user_id', '=', 1)->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('where')->once()->with('band_id', '=', 1)->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('count')->once()->andReturn(1);

		$result = $this->repo->isUserMember($band, $user);
		$this->assertTrue($result);
	}

	public function testIsUserAMemberReturnsFalseIfUserNotAMember()
	{
		$band = m::mock('Band');
		$user = m::mock('User');
		$band->shouldReceive('getAttribute')->with('id')->once()->andReturn(1);
		$user->shouldReceive('getAttribute')->with('id')->once()->andReturn(1);
		$this->mocks['db']->shouldReceive('table')->once()->with('band_user')->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('where')->once()->with('user_id', '=', 1)->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('where')->once()->with('band_id', '=', 1)->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('count')->once()->andReturn(0);

		$result = $this->repo->isUserMember($band, $user);
		$this->assertFalse($result);
	}

	public function testAddMemberAddsMemberIfUserIsNotAMemberYet()
	{
		$band = m::mock('Band');
		$user = m::mock('User');
		$band->shouldReceive('getAttribute')->twice()->with('id')->andReturn(1);
		$user->shouldReceive('getAttribute')->twice()->with('id')->andReturn(1);

		$this->mocks['db']->shouldReceive('table')->twice()->with('band_user')->andReturn($this->mocks['db']);

		$this->mocks['db']->shouldReceive('where')->once()->with('user_id', '=', 1)->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('where')->once()->with('band_id', '=', 1)->andReturn($this->mocks['db']);
		$this->mocks['db']->shouldReceive('count')->once()->andReturn(0);

		$this->mocks['db']->shouldReceive('insert')->once()->with(array(
			'band_id' => 1,
			'user_id' => 1
			));

		$this->repo->addMember($band, $user);
	}

	private function getMocks()
	{
		return array(
			'model' => m::mock('Band'),
			'db' => m::mock('Illuminate\Database\DatabaseManager')
			);
	}

	private function getRepo($mocks)
	{
		return new DatabaseBandRepository($mocks['model'], $mocks['db']);
	}
}