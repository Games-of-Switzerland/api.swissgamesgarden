@elasticsearch
Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to retrieve JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Accessing the Games Resource should require the page parameter.
    Given I send a "GET" request to "http://api.gos.test/search/games"
    Then the response status code should be 400
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "message" should be equal to "Something went wrong."
    And the JSON node "errors" should have 1 element
    And the JSON node "errors.page" should have 1 element
    And the JSON node "errors.page[0]" should be equal to 'This value should not be null.'

  Scenario: Games Resource mandatory page parameter should be zero or positive.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=-1"
    Then the response status code should be 500
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "message" should be equal to "Something went wrong with Elasticsearch."
    And the JSON node "errors" should have 1 element
#    And the JSON node "errors.page" should have 1 element
#    And the JSON node "errors.page[0]" should be equal to 'Please provide a page number when using the "list" scheme.'

  Scenario: Games Resource should respond with a paginated subset of games.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "hits.total" should be equal to "4"
    And the JSON node "hits.hits" should have 4 elements

  Scenario: The Games Resource facets/aggregations should follow a strict given structure.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 4 elements
    Then the JSON should be valid according to the schema "/var/www/behat/Fixtures/elasticsearch/schemaref.search.games.hits.json"

