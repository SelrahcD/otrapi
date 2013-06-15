<?php

use Illuminate\Auth\UserInterface,
	LaravelBook\Ardent\Ardent;

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

	/**
	 * The rules to be applied to the data.
	 *
	 * @var array
	 */
	public static $rules = array(
		'password' => 'required|min:6',
		'email' => 'required',
		);

	/**
	 * List of attribute names which should be hashed using the Bcrypt hashing algorithm.
	 *
	 * @var array
	 */
	public static $passwordAttributes = array('password');

	/**
	 * If set to true, the model will automatically replace all plain-text passwords
	 * attributes (listed in $passwordAttributes) with hash checksums
	 *
	 * @var bool
	 */
	public $autoHashPasswordAttributes = true;

	// protected function setPasswordAttribute($password)
	// {
	// 	$this->attributes['password'] = $password;
	// 	$this->attributes['password'] = Hash::make($password);
	// }


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

	/**
	 * Get user's bands
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function bands()
	{
		return $this->belongsToMany('Band');
	}
	

}