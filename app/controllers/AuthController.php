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
		// TODO : Check if both email and password index exist
		
		if(!$user = $this->userRepository->getUserByCredentials(Input::all()))
		{
			throw new AuthenticationException('Can\'t find user from credentials');
		}

		if(!$this->hasher->check(Input::get('password'), $user->password))
		{
			throw new AuthenticationException('Can\'t find user from credentials');
		}

		// If we can't find a valid token for user create a new one
		if(!$token = $this->tokenRepository->getForUser($user))
		{
			$token = $this->tokenFactory->createTokenForUser($user);
			
			$this->tokenRepository->store($token);
		}

		return $token;
	}

	/**
	 * Create a new token for user
	 * 
	 * @return Token
	 */
	public function refreshToken()
	{
		if(!$tokenId = Request::getUser())
		{
			throw new AuthenticationException;
		}

		if(!$user = $this->userRepository->getUserByToken($tokenId, false))
		{
			throw new AuthenticationException;
		}

		if(!$token = $this->tokenRepository->getForUser($user, false))
		{
			throw new AuthenticationException;
		}

		if(Input::get('refresh_token') !== $token->getRefresh())
		{
			throw new ErrorMessageException('Unvalid refresh token');
		}

		// Delete old token
		$this->tokenRepository->delete($token);

		// Create new token and store it
		$token = $this->tokenFactory->createTokenForUser($user);
		$this->tokenRepository->store($token);

		return $token;
	}
}