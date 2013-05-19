<?php

use Illuminate\Hashing\HasherInterface;

class AuthController extends BaseController {

	/**
	 * Users repository
	 * 
	 * @var UserRepositoryInterface
	 */
	protected $userRepository;

	/**
	 * Token repository
	 * 
	 * @var TokenRepositoryInterface
	 */
	protected $tokenRepository;

	protected $tokenFactory;

	protected $hasher;

	public function __construct(TokenFactory $tokenFactory, TokenRepositoryInterface $tokenRepository, UserRepositoryInterface $userRepository, HasherInterface $hasher)
	{
		$this->tokenFactory    = $tokenFactory;
		$this->tokenRepository = $tokenRepository;
		$this->userRepository  = $userRepository;
		$this->hasher          = $hasher;
	}

	public function getToken()
	{

		if(!($user = $this->userRepository->getUserByCredentials(Input::all())))
		{
			throw new AuthenticationException;
		}

		if(!$this->hasher->check(Input::get('password'), $user->password))
		{
			throw new AuthenticationException;
		}

		$token = $this->tokenFactory->createTokenForUser($user);
		
		$this->tokenRepository->store($token);

		return $token;
	}
}