<?php

class DatabaseBandRepository implements BandRepositoryInterface {
	
	/**
	 * Eloquent model
	 * 
	 * @var Band
	 */
	protected $model;

	/**
	 * Constructor
	 * 
	 * @param Band $model
	 */
	public function __construct(Band $model)
	{
		$this->model = $model;
	}

	public function make($data = array())
	{
		return $this->model->newInstance($data);
	}

	public function store(Band $band)
	{
		$band->save();
	}

	public function get($id)
	{
		return $this->model->find($id);
	}
}