<?php

use Illuminate\Support\ServiceProvider;

class AuthTokenServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['TokenFactory'] = $this->app->share(function($app)
		{
			return new TokenFactory(10800);
		});

		$this->app['command.token.clean'] = $this->app->share(function($app)
		{
			return new TokenCleanCommand($app['TokenRepositoryInterface']);
		});

		$this->app['command.token.delete'] = $this->app->share(function($app)
		{
			return new TokenDeleteCommand($app['TokenRepositoryInterface']);
		});

		$this->commands('command.token.clean');
		$this->commands('command.token.delete');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('command.token.clean', 'command.token.delete', 'TokenFactory');
	}
	
}