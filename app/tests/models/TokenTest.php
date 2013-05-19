<?php
 
use Mockery as m;

class TokenTest extends TestCase {

	protected $factory;

	public function tearDown()
	{
		m::close();
	}

	public function setUp()
	{
		$this->factory = new TokenFactory(1);
	}

	public function testIsValidReturnsTrueIfTokenIsValid()
	{
		$token = new Token;
		$expiration = new DateTime ;
		$interval = new DateInterval('PT30S');
		$expiration->add($interval);

		$token->fill(array(
			'expiration' => $expiration
			)
		);

		$this->assertTrue($token->isValid());
	}

	public function testIsValidReturnsFalseIfTokenIsntValid()
	{
		$token = new Token;
		$expiration = new DateTime ;
		$interval = new DateInterval('PT30S');
		$expiration->sub($interval);

		$token->fill(array(
			'expiration' => $expiration
			)
		);
		$this->assertFalse($token->isValid());
	}
}