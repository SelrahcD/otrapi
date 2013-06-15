Feature: Authentication

Scenario: A command can delete expired sessions from database
	Given that I'm in the root directory
	When I run artisan's task token:clean
	Then I should get:
	"""
	All expired tokens were deleted.
	"""

Scenario: A command can delete all tokens
	Given that I'm in the root directory
	When I run artisan's task token:delete
	Then I should get:
	"""
	All tokens were deleted.
	"""
Scenario: I can get a token if I provide valid authentication data
	Given that email is "c.desneuf@gmail.com"
	Given that password is "password"
	When I make a POST request on "/auth"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains expiration
	Then the response contains token
	Then the response contains refresh_token

Scenario: I cant get a token if I provide unvalid email
	Given that email is "c.desneuf@test.com"
	Given that password is "password"
	When I make a POST request on "/auth"
	Then the response is JSON
	Then the response status code is 401

Scenario: I cant get a token if I provide unvalid password
	Given that email is "c.desneuf@gmail.com"
	Given that password is "wrongPassword"
	When I make a POST request on "/auth"
	Then the response is JSON
	Then the response status code is 401

Scenario: I can refresh my token
	Given that email is "c.desneuf@gmail.com"
	Given that password is "password"
	When I make a POST request on "/auth"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains expiration
	Then the response contains token
	Then the response contains refresh_token
	Given that I'm connected as user c.desneuf@gmail.com
	Given that refresh_token is "{refresh_token}"
	When I make a POST request on "/auth/refresh"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains expiration
	Then the response contains token
	Then the response contains refresh_token
	Then I store the response for user indentification

Scenario: If refresh token isn't valid I get a 400 status code
	Given that email is "c.desneuf@gmail.com"
	Given that password is "password"
	When I make a POST request on "/auth"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains expiration
	Then the response contains token
	Then the response contains refresh_token
	Given that I'm connected as user c.desneuf@gmail.com
	Given that refresh_token is "badToken"
	When I make a POST request on "/auth/refresh"
	Then the response is JSON
	Then the response status code is 400

Scenario: If I don't provide a token for refresh I get a 401
	When I make a POST request on "/auth/refresh"
	Then the response is JSON
	Then the response status code is 401