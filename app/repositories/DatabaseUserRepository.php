<?php

class DatabaseUserRepository implements UserRepositoryInterface {

	/* Eloquent model
	 * 
	 * @var User
	 */	
	private $model;

	/**
	 * Repository Constructor
	 * 
	 * @param User $model
	 */
	function __construct(User $model)
	{
		$this->model = $model;
	}

	/**
	 * Create a user from data
	 *
	 * @param  $data
	 * @return User
	 */
	public function make($data = array())
	{
		return $this->model->newInstance($data);
	}

	/**
	 * Get a user using his credentials
	 * 
	 * @param  array  $credentials Should contain a email key
	 * @return User | null
	 */
	public function getUserByCredentials(array $credentials)
	{
		return $this->model->newQuery()->where('email', '=', $credentials['email'])->first();
	}

	/**
	 * Store an user
	 * 
	 * @param  User   $user
	 * @return bool
	 */
	public function store(User $user)
	{
		$user->save();
	}

}