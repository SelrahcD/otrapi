<?php

App::bind('TokenRepositoryInterface', function()
	{
		return new FluentTokenRepository(App::make('db'));
	});

App::bind('UserRepositoryInterface', function()
	{
		return new FluentUserRepository(App::make('db'));
	});

App::instance('Illuminate\Hashing\HasherInterface', App::make('hash'));
