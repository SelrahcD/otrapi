Feature: Users

Scenario: I can create a user
	Given that email is "paul@test.fr"
	Given that password is "paulPassword"
	When I make a POST request on "/users"
	Then the response is JSON
	Then the response status code should be 200
	Then the response should contain id
	Then the response should contain email and is "paul@test.fr"
	Then the response should not contain password