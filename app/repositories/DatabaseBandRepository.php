<?php

use Illuminate\Database\DatabaseManager;

class DatabaseBandRepository implements BandRepositoryInterface {
	
	/**
	 * Eloquent model
	 * 
	 * @var Band
	 */
	protected $model;

	/**
	 * Database manager
	 * 
	 * @var DatabaseManager
	 */
	protected $db;

	/**
	 * Name of the pivot table between bands and user
	 * 
	 * @var string
	 */
	protected $bandUserPivotTable = 'band_user';

	/**
	 * Constructor
	 * 
	 * @param Band $model
	 */
	public function __construct(Band $model, DatabaseManager $db)
	{
		$this->model = $model;
		$this->db = $db;
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

	public function isUserMember(Band $band, User $user)
	{
		return $this->db->table($this->bandUserPivotTable)->where('user_id', '=', $user->id)->where('band_id', '=', $band->id)->count() == 1;
	}

	public function addMember(Band $band, User $user)
	{
		if(!$this->isUserMember($band, $user))
		{
			return $this->db->table($this->bandUserPivotTable)->insert(array(
				'band_id' => $band->id,
				'user_id' => $user->id
				));
		}
	}

}