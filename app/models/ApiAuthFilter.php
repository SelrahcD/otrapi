<?php

class ApiAuthFilter {

	/**
	 * User repository
	 * 
	 * @var UserRepositoryInterface
	 */
	protected $userRepo;

	/**
	 * Constructor
	 * @param UserRepositoryInterface $userRepo
	 */
	public function __construct(UserRepositoryInterface $userRepo)
	{
		$this->userRepo = $userRepo;
	}

	public function filter()
	{
		if(!($token = Request::getUser()))
		{
			throw new AuthenticationException;
		}

		if(!($user = $this->userRepo->getUserByToken($token)))
		{
			throw new AuthenticationException;
		}

		Auth::login($user);
	}

}