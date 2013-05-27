<?php

use Illuminate\Auth\UserInterface;

class User extends Ardent implements UserInterface {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array('id', 'email', 'password', 'created_at', 'updated_at');

	/**
	 * The attributes that should be visible in arrays.
	 *
	 * @var arrays
	 */
	protected $visible = array('id', 'email', 'created_at', 'updated_at');

	protected function setPasswordAttribute($password)
	{
		$this->attributes['password'] = Hash::make($password);
	}


	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}
}