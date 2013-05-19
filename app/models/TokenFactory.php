<?php

class TokenFactory {
	
	public function createTokenForUser(User $user)
	{
		$attributes = array('user_id' => $user->id);
		$token = new Token($attributes);
		$this->initToken($token);
		return $token;
	}

	protected function initToken(Token $token)
	{
		$this->setId($token);
		$this->setExpiration($token);
	}

	protected function setId(Token $token)
	{
		$attributes = array('id' => 'aaaaa');
		$token->fill($attributes);
	}

	protected function setExpiration(Token $token)
	{
		$attributes = array('expiration' => time() + 1000);
		$token->fill($attributes);
	}
}