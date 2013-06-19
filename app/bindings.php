<?php

App::bind('TokenRepositoryInterface', function()
	{
		return new FluentTokenRepository(App::make('db'));
	});

App::bind('UserRepositoryInterface', function()
	{
		return new DatabaseUserRepository(new User);
	});

App::bind('BandRepositoryInterface', function()
	{
		return new DatabaseBandRepository(new Band, App::make('db'));
	});

App::instance('Illuminate\Hashing\HasherInterface', App::make('hash'));
