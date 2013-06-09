Feature: Authentication

Scenario: I can get a token if I provide valid authentication data
	Given that email is "c.desneuf@gmail.com"
	Given that password is "password"
	When I make a POST request on "/auth"
	Then the response is JSON
	Then the response status code is 200

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