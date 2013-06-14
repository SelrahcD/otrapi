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
			'refresh'	 => $token->getRefresh(),
			'user_id'    => $token->getUserId(),
			'expiration' => $token->getExpiration(),
			);

		$this->newQuery()->insert($tokenArray);
	}

	public function delete(Token $token)
	{
		$this->newQuery()->where('id', '=', $token->getId())->delete();
	}

	public function deleteExpired()
	{
		$this->newQuery()->where('expiration', '<', new DateTime)->delete();
	}

	public function deleteAll()
	{
		$this->newQuery()->truncate();
	}

	public function getForUser(User $user, $expired = true)
	{
		$requete = $this->newQuery()->where('user_id', '=', $user->id);

		if($expired)
		{
			$requete->where('expiration', '>', new DateTime);
		}

		$data = $requete->first();

		return $data ? new Token(get_object_vars($data)) : null;
	}

	protected function newQuery()
	{
		return $this->db->table($this->tableName);
	}
}