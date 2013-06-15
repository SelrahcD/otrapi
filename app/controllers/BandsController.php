<?php

class BandsController extends BaseController {

	/**
	 * Bands repository
	 * 
	 * @var BandRepositoryInterface
	 */
	protected $repo;

	public function __construct(BandRepositoryInterface $repo)
	{
		$this->repo = $repo;
	}
	

	public function create()
	{
		$band = $this->repo->make(Input::all());

		if(!$band->validate())
		{
			throw new ValidationException($band->errors());
		}

		$this->repo->store($band);

		return $band;
	}


	public function showMembers($bandId)
	{
		if(!($band = $this->repo->get($bandId)))
		{
			throw new NotFoundException;
		}

		return $band->users;
	}
}