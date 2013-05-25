<?php

class UsersController extends BaseController {

	/**
	 * Users repository
	 * 
	 * @var UserRepositoryInterface
	 */
	protected $userRepo;

	/**
	 * Constructor
	 * 
	 * @param UserRepositoryInterface $userRepo
	 */
	public function __construct(UserRepositoryInterface $userRepo)
	{
		$this->userRepo = $userRepo;
	}

	/**
	 * Create a new user
	 * 
	 * @return User
	 */
	public function create()
	{
		$user = $this->userRepo->make(Input::all());

		if(!$user->validate())
		{
			throw new ValidationException($user->errors());
		}

		$this->userRepo->store($user);

		return $user;
	}

	/**
	 * Show current user
	 * 
	 * @return User
	 */
	public function showMe()
	{
		return Auth::user();
	}

	/**
	 * Edit current user
	 * 
	 * @return User
	 */
	public function editMe()
	{
		$user = Auth::user();

		$user->fill(Input::all());

		if(!$user->validate())
		{
			throw new ValidationException($user->errors());
		}

		$this->userRepo->store($user);

		return $user;
	}
	
}