Feature: Bands

Scenario: A authenticated user can create a band
	Given that I'm connected as user c.desneuf@gmail.com
	Given that name is "Taskane"
	When I make a POST request on "/bands"
	Then the response is JSON
	Then the response status code should be 200
	Then the response should contain id
	Then the response should contain name and is "Taskane"