<?php

class BandsController extends BaseController {

	/**
	 * Bands repository
	 * 
	 * @var BandRepositoryInterface
	 */
	protected $bandRepository;

	/**
	 * Users repository
	 * 
	 * @var UserRepositoryInterface
	 */
	protected $userRepository;

	public function __construct(BandRepositoryInterface $bandRepository, UserRepositoryInterface $userRepository)
	{
		$this->bandRepository = $bandRepository;
		$this->userRepository = $userRepository;
	}
	

	public function create()
	{
		$band = $this->bandRepository->make(Input::all());

		if(!$band->validate())
		{
			throw new ValidationException($band->errors());
		}

		$this->bandRepository->store($band);

		$band->users()->attach(Auth::user());

		return $band;
	}


	public function showMembers($bandId)
	{
		if(!($band = $this->bandRepository->get($bandId)))
		{
			throw new NotFoundException;
		}

		return $band->users;
	}

	public function addMember($bandId)
	{
		if(!($band = $this->bandRepository->get($bandId)))
		{
			throw new NotFoundException;
		}

		if(!$user = $this->userRepository->get(Input::get('user_id')))
		{
			throw new NotFoundException;
		}

		$band->users()->attach($user);

		return $band->users;
	}
}