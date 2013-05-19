<?php

use Illuminate\Console\Command;

class TokenDeleteCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'token:delete';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Delete all tokens";

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
		$this->tokenRepository->deleteAll();

		$this->info("All tokens were deleted.");
	}

}