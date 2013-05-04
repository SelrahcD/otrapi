<?php

use Illuminate\Support\MessageBag;
use Illuminate\Support\Contracts\MessageProviderInterface;

class ValidationException extends RuntimeException
{
	/**
	 * The error messages.
	 *
	 * @var Illuminate\Support\MessageBag
	 */
	protected $messages;

	/**
	 * Create new ErrorMessageException.
	 *
	 * @param  mixed  $messages
	 * @return void
	 */
	public function __construct($messages)
	{
		// Make sure we're working with a MessageBag
		if (!($messages instanceof MessageProviderInterface))
		{
			$messages = new MessageBag((array) $messages);
		}

		$this->messages = $messages->getMessageBag();
		$this->messages->setFormat(':message');
	}

	/**
	 * Return error messages.
	 *
	 * @return array
	 */
	public function getMessagesAsArray()
	{
		return $this->messages->getMessages();
	}
}