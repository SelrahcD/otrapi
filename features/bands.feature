Feature: Bands

Scenario: A authenticated user can create a band and is band member
	Given that I'm connected as user c.desneuf@gmail.com
	Given that name is "Taskane"
	When I make a POST request on "/bands"
	Then the response is JSON
	Then the response status code is 200
	Then the response contains id
	Then the response contains name and is "Taskane"