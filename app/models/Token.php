<?php

use Illuminate\Support\Contracts\JsonableInterface;

class Token implements JsonableInterface {

	/**
	 * Token id
	 * 
	 * @var string
	 */
	protected $id;

	/**
	 * Refresh token
	 * 
	 * @var string
	 */
	protected $refresh;

	/**
	 * User id
	 * 
	 * @var int
	 */
	protected $user_id;

	/**
	 * Expiration time
	 * 
	 * @var DateTime
	 */
	protected $expiration;
	
	/**
	 * Constructor
	 * 
	 * @param array $attributes
	 */
	public function __construct(array $attributes = array())
	{
		$this->fill($attributes);
	}

	/**
	 * Fill object with attributes
	 * 
	 * @param  array  $attributes
	 * @return void 
	 */
	public function fill(array $attributes = array())
	{
		$attributesList = array('id', 'refresh', 'user_id', 'expiration');

		foreach($attributesList as $attributeName)
		{
			if(array_key_exists($attributeName, $attributes))
			{
				$this->{$attributeName} = $attributes[$attributeName];
			}
		}
	}

	/**
	 * Get User id
	 * 
	 * @return int
	 */
	public function getUserId()
	{
		return $this->user_id;
	}

	/**
	 * Get Expiration
	 * 
	 * @return int
	 */
	public function getExpiration()
	{
		return $this->expiration;
	}

	/**
	 * Get token id
	 * 
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get refresh token
	 * 
	 * @return string
	 */
	public function getRefresh()
	{
		return $this->refresh;
	}

	/**
	 * Indicate if the token is valid
	 * 
	 * @return boolean
	 */
	public function isValid()
	{
		return (bool) $this->expiration->diff( new DateTime )->invert;
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'token'         => $this->id,
			'expiration'    => $this->expiration,
			'refresh_token' => $this->refresh,
			);
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int  $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}
}