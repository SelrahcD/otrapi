<?php

// ------------------------------------------------------------
// Error Handlers
// ------------------------------------------------------------

App::error(function(Exception $e, $code)
{
	Log::error($e);

	$default_message = 'An error occured while processing the request';

	return Response::json(array(
		'error' => $e->getMessage() ?: $default_message,
	), 500);
});

// General HttpException handler
App::error(function(Symfony\Component\HttpKernel\Exception\HttpException $e, $code)
{
	$headers = $e->getHeaders();

	switch ($code)
	{
		case 401:
			$default_message = 'Invalid API key';
			$headers['WWW-Authenticate'] = 'Basic realm="REST API"';
		break;

		case 403:
			$default_message = 'Insufficient privileges to perform this action';
		break;

		case 404:
			$default_message = 'The requested resource was not found';
		break;

		default:
			$default_message = 'An error was encountered';
	}

	return Response::json(array(
		'error' => $e->getMessage() ?: $default_message,
	), $code, $headers);
});

// ErrorMessageException handler
App::error(function(ErrorMessageException $e)
{
	$messages = $e->getMessages()->all();

	return Response::json(array(
		'error' => $messages[0],
	), 400);
});

// ValidationException handler
App::error(function(ValidationException $e)
{
	$messages = $e->getMessagesAsArray();

	return Response::json(array(
		'error' => $messages,
	), 400);
});

// NotFoundException handler
App::error(function(NotFoundException $e)
{
	$default_message = 'The requested resource was not found';

	return Response::json(array(
		'error' => $e->getMessage() ?: $default_message,
	), 404);
});

// PermissionException handler
App::error(function(PermissionException $e)
{
	$default_message = 'Insufficient privileges to perform this action';

	return Response::json(array(
		'error' => $e->getMessage() ?: $default_message,
	), 403);
});

// AuthenticationException handler
App::error(function(AuthenticationException $e)
{
	$default_message = 'Invalid API key';
	$headers['WWW-Authenticate'] = 'Basic realm="REST API"';

	return Response::json(array(
		'error' => $e->getMessage() ?: $default_message,
	), 401, $headers);
});