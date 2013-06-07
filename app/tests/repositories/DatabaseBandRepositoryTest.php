<?php

use Mockery as m;

class DatabaseBandRepositoryTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		parent::setUp();

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

	private function getMocks()
	{
		return array(
			'model' => m::mock('Band'),
			);
	}

	private function getRepo($mocks)
	{
		return new DatabaseBandRepository($mocks['model']);
	}
}