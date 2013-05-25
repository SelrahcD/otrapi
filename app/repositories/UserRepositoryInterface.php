<?php

interface UserRepositoryInterface {
	
	public function make($attributes = array());

	public function getUserByCredentials(array $credentials);

	public function getUserByToken($token);

	public function store(User $user);
	
}