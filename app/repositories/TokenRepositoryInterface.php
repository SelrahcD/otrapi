<?php

interface TokenRepositoryInterface {
	
	public function store(Token $token);

	public function deleteExpiredTokens();
}