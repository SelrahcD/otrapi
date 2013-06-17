<?php

interface UserRepositoryInterface {
	
	public function make($attributes = array());

	public function get($id);

	public function getUserByCredentials(array $credentials);

	public function getUserByToken($token, $expired = true);

	public function store(User $user);
	
}