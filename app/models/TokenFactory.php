<?php

class TokenFactory {

	/**
	 * Duration of token validity
	 * 
	 * @var int
	 */
	protected $sessionDuration;

	/**
	 * Constructor
	 * 
	 * @param int $sessionDuration (Default to 3 days)
	 */
	public function __construct($sessionDuration = 10800)
	{
		$this->sessionDuration = $sessionDuration;
	}
	
	/**
	 * Create a token for user
	 * 
	 * @param  User   $user
	 * @return Token 
	 */
	public function createTokenForUser(User $user)
	{
		$attributes = array('user_id' => $user->id);
		$token = new Token($attributes);
		$this->initToken($token);
		return $token;
	}

	/**
	 * Init a token
	 * 
	 * @param  Token  $token
	 * @return void 
	 */
	protected function initToken(Token $token)
	{
		$this->setId($token);
		$this->setExpiration($token);
	}

	/**
	 * Set id to token
	 * 
	 * @param Token $token
	 */
	protected function setId(Token $token)
	{
		$id = md5(uniqid(rand(), true));
		$attributes = array('id' => $id);
		$token->fill($attributes);
	}

	/**
	 * Set expiration date to token
	 * 
	 * @param Token $token
	 */
	protected function setExpiration(Token $token)
	{
		$expiration = new DateTime;
		$interval = new DateInterval('PT'.$this->sessionDuration.'S');
		$expiration->add($interval);
		$attributes = array('expiration' => $expiration);
		$token->fill($attributes);
	}
}