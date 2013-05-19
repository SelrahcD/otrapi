<?php

interface UserRepositoryInterface {
	
	public function getUserByCredentials(array $credentials);
	
}