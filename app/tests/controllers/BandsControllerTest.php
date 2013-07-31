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
	}

	public function testCreateBandStoresBandIfValidAndReturnsIt()
	{
		$this->be($user = new User);
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('store')->once();

		$this->mocks['bandRepository']->shouldReceive('addMember')->once()->with('Band', $user);

		$input = array(
			'name' => 'My band name',
			);

		$response = $this->call('POST', 'bands', $input);
		$this->assertInstanceOf('Band', $response->getOriginalContent());
	}

	/**
	 * @expectedException ValidationException
	 */
	public function testCreateThrowsValidationExceptionIfDataIsntValid()
	{
		$input = array(
			'name' => '',
			);

		$this->call('POST', 'bands', $input);
	}

	/**
	 * @expectedException NotFoundException
	 */
	public function testShowThrowsNotFoundExceptionIfBandIsNotFound()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn(null);

		$this->call('GET', 'bands/1');
	}

	public function testShowReturnsTheBandIfFound()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn('band');

		$response = $this->call('GET', 'bands/1');
		$this->assertEquals('band', $response->getOriginalContent());
	}

	/**
	 * @expectedException NotFoundException
	 */
	public function testEditThrowsNotFoundExceptionIfBandIsNotFound()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn(null);

		$this->call('PUT', 'bands/1');
	}


	/**
	 * @expectedException ValidationException
	 */
	public function testEditThrowsValidationExceptionIfDataIsntValid()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn(new Band);

		$input = array(
			'name' => '',
			);

		$this->call('PUT', 'bands/1', $input);
	}

	public function testEditReturnsBandAfterStoringItIfOk()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn($band = new Band);
		
		$this->mocks['bandRepository']->shouldReceive('store')->once()->with($band);

		$input = array(
			'name' => 'My band name',
			);

		$this->call('PUT', 'bands/1', $input);
	}

	/**
	 * @expectedException NotFoundException
	 */
	public function testShowMembersThrowsNotFoundExceptionIfBandIsNotFound()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn(null);

		$this->call('GET', 'bands/1/members');
	}

	public function testShowMembersReturnsMembers()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn($band = m::mock('Band'));

		$band->shouldReceive('getAttribute')->once()->with('users')->andReturn('members');

		$response = $this->call('GET', 'bands/1/members');
		$this->assertEquals('members', $response->getOriginalContent());
	}

	/**
	 * @expectedException NotFoundException
	 */
	public function testAddMemberThrowsNotFoundExceptionIfBandIsNotFound()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn(null);

		$this->call('POST', 'bands/1/members');
	}

	/**
	 * @expectedException NotFoundException
	 */
	public function testAddMemberThrowsNotFoundExceptionIfUIsNotserFound()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);
		$this->app->instance('UserRepositoryInterface', $this->mocks['userRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn(new Band);

		$this->mocks['userRepository']->shouldReceive('get')->once()->with(12)->andReturn(null);

		$input = array(
			'user_id' => 12,
			);

		$this->call('POST', 'bands/1/members', $input);
	}

	public function testAddMemberAddsUserToBandMembers()
	{
		$this->app->instance('BandRepositoryInterface', $this->mocks['bandRepository']);
		$this->app->instance('UserRepositoryInterface', $this->mocks['userRepository']);

		$this->mocks['bandRepository']->shouldReceive('get')->once()->with(1)->andReturn($band = new Band);

		$this->mocks['userRepository']->shouldReceive('get')->once()->with(12)->andReturn($user = new User);

		$this->mocks['bandRepository']->shouldReceive('addMember')->once()->with($band, $user);

		$input = array(
			'user_id' => 12,
			);

		$response = $this->call('POST', 'bands/1/members', $input);
		$this->assertEquals('204', $response->getStatusCode());
	}

	protected function getMocks()
	{
		$bandRepositoryOriginal = $this->app->make('BandRepositoryInterface');
		$bandRepository = m::mock($bandRepositoryOriginal);

		$userRepositoryOriginal = $this->app->make('UserRepositoryInterface');
		$userRepository = m::mock($userRepositoryOriginal);

		return array(
			'bandRepository' => $bandRepository,
			'userRepository' => $userRepository,
			);
	}
}