<?php

use Illuminate\Console\Command;

class TokenCleanCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'token:clean';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Delete expired tokens";

	/**
	 * Token repository
	 * 
	 * @var TokenRepositoryInterface
	 */
	protected $tokenRepository;

	/**
	 * Create a new clean token command.
	 * 
	 * @return void
	 */
	public function __construct(TokenRepositoryInterface $tokenRepository)
	{
		$this->tokenRepository = $tokenRepository;

		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->tokenRepository->deleteExpiredTokens();

		$this->info("All expired tokens were deleted.");
	}

	/**
	 * Generate a random key for the application.
	 *
	 * @return string
	 */
	protected function getRandomKey()
	{
		return Str::random(32);
	}

}