Feature: Authentication

Scenario: I can get a token if I provide valid connection
	Given that my email is "c.desneuf@gmail.com"
	Given that my password is "password"
	When I make a POST request on "/auth"
	Then the response is JSON
	Then the response status code should be 200
	