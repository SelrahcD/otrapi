<?php

class User extends Eloquent {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array('id', 'email', 'password', 'created_at', 'updated_at');

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	protected function setPasswordAttribute($password)
	{
		$this->attributes['password'] = Hash::make($password);
	}

	public function validate()
	{
		return true;
	}
}