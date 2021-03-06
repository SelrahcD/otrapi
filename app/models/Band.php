<?php

class Band extends Ardent {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array('name');

	/**
	 * The attributes that should be visible in arrays.
	 *
	 * @var arrays
	 */
	protected $visible = array('id', 'name', 'created_at', 'updated_at');

	/**
	 * The rules to be applied to the data.
	 *
	 * @var array
	 */
	public static $rules = array(
		'name' => 'required',
		);

	/**
	 * Get bands members
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany('User');
	}
	
	
}