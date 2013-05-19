<?php

use Illuminate\Database\DatabaseManager;

class FluentUserRepository implements UserRepositoryInterface {

	protected $tableName = 'users';

	protected $db;

	public function __construct(DatabaseManager $db)
	{
		$this->db = $db;
	}

	public function make($attributes = array())
	{
		return $this->buildUser($attributes);
	}
	
	public function getUserByCredentials(array $credentials)
	{
		$attributes = $this->newQuery()->where('email', '=', $credentials['email'])->first();
		return $this->buildUser($attributes);
	}

	protected function buildUser($attributes, $exists = false)
	{
		if(is_null($attributes))
		{
			return null;
		}
		
		if(is_object($attributes))
		{
			$attributes = (array) $attributes;
		}

		$user = new User($attributes);
		$user->exists = $exists;

		return $user;
	}

	protected function newQuery()
	{
		return $this->db->table($this->tableName);
	}

}