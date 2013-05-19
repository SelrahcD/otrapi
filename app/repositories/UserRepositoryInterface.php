<?php

interface UserRepositoryInterface {
	
	public function make($attributes = array());
	
	public function getUserByCredentials(array $credentials);
	
}