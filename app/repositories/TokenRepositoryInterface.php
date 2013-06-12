<?php

interface TokenRepositoryInterface {
	
	public function store(Token $token);

	public function deleteExpired();

	public function deleteAll();

	public function getForUser(User $user);
}