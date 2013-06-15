<?php

use Mockery as m;

class BandsControllerTest extends TestCase {

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

	public function testCreateBandStoresBandIfValidAndReturnsIt()
	{
		Input::shouldReceive('all')->once()->andReturn($input = 'input');
		$this->mocks['repo']->shouldReceive('make')->once()->with($input)->andReturn($band = m::mock('Band'));
		$band->shouldReceive('validate')->once()->andReturn(true);
		$this->mocks['repo']->shouldReceive('store')->once()->with($band);
		$response = $this->controller->create();
		$this->assertEquals($band, $response);
	}

	/**
	 * @expectedException ValidationException
	 */
	public function testCreateThrowsValidationExceptionIfDataIsntValid()
	{
		Input::shouldReceive('all')->once()->andReturn($input = 'input');
		$this->mocks['repo']->shouldReceive('make')->once()->with($input)->andReturn($band = m::mock('Band'));
		$band->shouldReceive('validate')->once()->andReturn(false);
		$band->shouldReceive('errors')->once()->andReturn($errors = array());
		$this->controller->create();
	}

	/**
	 * @expectedException NotFoundException
	 */
	public function testShowMembersThrowsNotFoundExceptionIfBandIsNotFound()
	{
		$this->mocks['repo']->shouldReceive('get')->once()->with(1)->andReturn(null);
		$this->controller->showMembers(1);
	}

	protected function getMocks()
	{
		return array(
			'repo' => Mockery::mock('BandRepositoryInterface'));
	}

	protected function getController(Array $mocks = array())
	{
		return new BandsController($mocks['repo']);
	}
}