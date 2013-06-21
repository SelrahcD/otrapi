Feature: Bands

Scenario: A authenticated user can create a band and is band member
	Given that I'm connected as user user1@test.fr
	Given that name is "band3"
	When I make a POST request on "/bands"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains id
	Then the response contains name and is "band3"
	When I make a GET request on "/bands/{id}/members"
	Then the response status code is 200
	Then the response contains *.email and is "user1@test.fr"

Scenario: A authenticated user can see a band
	Given that I'm connected as user user1@test.fr
	When I make a GET request on "/bands/1"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains id
	Then the response contains name

Scenario: A authenticated user can edit a band
	Given that I'm connected as user user1@test.fr
	Given that name is "band1-up" 
	When I make a PUT request on "/bands/1"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains id
	Then the response contains name and is "band1-up"

Scenario: A authenticated user can add a user to a band
	Given that I'm connected as user user1@test.fr
	Given that user_id is "2"
	When I make a POST request on "/bands/1/members"
	Then the response status code is 204