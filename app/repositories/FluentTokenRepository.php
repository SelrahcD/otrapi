<?php

use Illuminate\Database\DatabaseManager;

class FluentTokenRepository implements TokenRepositoryInterface {

	protected $tableName = 'tokens';

	protected $db;

	public function __construct(DatabaseManager $db)
	{
		$this->db = $db;
	}

	public function store(Token $token)
	{
		$tokenArray = array(
			'id'         => $token->getId(),
			'user_id'    => $token->getUserId(),
			'expiration' => $token->getExpiration(),
			);

		$this->newQuery()->insert($tokenArray);
	}

	public function deleteExpired()
	{
		$this->newQuery()->where('expiration', '<', new DateTime)->delete();
	}

	public function deleteAll()
	{
		$this->newQuery()->truncate();
	}

	public function getForUser(User $user)
	{
		$data = $this->newQuery()->where('user_id', '=', $user->id)->where('expiration', '>', new DateTime)->first();

		return $data ? new Token(get_object_vars($data)) : null;
	}

	protected function newQuery()
	{
		return $this->db->table($this->tableName);
	}
}