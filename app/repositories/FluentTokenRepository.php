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

	protected function newQuery()
	{
		return $this->db->table($this->tableName);
	}
}