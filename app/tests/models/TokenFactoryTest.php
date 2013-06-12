<?php
 
use Mockery as m;

class TokenFactoryTest extends TestCase {

	protected $factory;

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		$this->factory = new TokenFactory(1);
	}

	public function testCreateTokenForUserReturnsAValidToken()
	{
		$user = m::mock('User');
		$user->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);
		$token = $this->factory->createTokenForUser($user);
		$this->assertInstanceOf('Token', $token);
		$this->assertEquals(1, $token->getUserId());
		$this->assertNotNull($token->getId());
		$this->assertNotNull($token->getRefresh());
		$this->assertNotNull($token->getExpiration());
		$this->assertInstanceOf('DateTime', $token->getExpiration(true));
	}
}