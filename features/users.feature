Feature: Users

Scenario: I can create a user
	Given that email is "paul@test.fr"
	Given that password is "paulPassword"
	When I make a POST request on "/users"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains id
	Then the response contains email and is "paul@test.fr"
	Then the response doesn't contain password

Scenario: If I create a user with invalid data I get an error
	Given that email is "paul@test.fr"
	Given that password is "a"
	When I make a POST request on "/users"
	Then the response is JSON
	Then the response status code is 400
	Then the response contains error.password

Scenario: A user can view his profile
	Given that I'm connected as user c.desneuf@gmail.com
	When I make a GET request on "/me"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains id
	Then the response contains email and is "c.desneuf@gmail.com"
	Then the response doesn't contain password

Scenario: A user can edit his profile
	Given that I'm connected as user paul@test.fr
	Given that email is "paul@test2.fr"
	When I make a PUT request on "/me"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains id
	Then the response contains email and is "paul@test2.fr"
	Then the response doesn't contain password

Scenario: If a user update his profile with invalid password he gets an error
	Given that I'm connected as user paul@test.fr
	Given that password is "a"
	When I make a PUT request on "/me"
	Then the response is JSON
	Then the response status code is 400
	Then the response contains error.password