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

	/**
	 * Token factory
	 * 
	 * @var TokenFactory
	 */
	protected $tokenFactory;

	/**
	 * Hasher
	 * 
	 * @var Illuminate\Hashing\HasherInterface
	 */
	protected $hasher;

	/**
	 * Constructor
	 * @param TokenFactory             $tokenFactory    
	 * @param TokenRepositoryInterface $tokenRepository 
	 * @param UserRepositoryInterface  $userRepository  
	 * @param Illuminate\Hashing\HasherInterface          $hasher          
	 */
	public function __construct(TokenFactory $tokenFactory, TokenRepositoryInterface $tokenRepository, UserRepositoryInterface $userRepository, HasherInterface $hasher)
	{
		$this->tokenFactory    = $tokenFactory;
		$this->tokenRepository = $tokenRepository;
		$this->userRepository  = $userRepository;
		$this->hasher          = $hasher;
	}

	/**
	 * Authenticate a user with credentials and return a token if user is ok
	 * 
	 * @return Token
	 */
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