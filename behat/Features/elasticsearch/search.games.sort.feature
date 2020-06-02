@elasticsearch
Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to fetch sortable Games in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with score sortable properties.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&sort[desc]=_score"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.total" should be equal to "4"
    And the JSON node "hits.hits" should have 4 elements
    And the JSON node "hits.hits[0]._source.title" should be equal to "Farming Simulator 18"
    And the JSON node "hits.hits[1]._source.title" should be equal to "Farming Simulator 19"
    And the JSON node "hits.hits[2]._source.title" should be equal to "Don't kill Her"
    And the JSON node "hits.hits[3]._source.title" should be equal to "Persephone"

  Scenario: Games Resource should respond with title sortable properties.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&sort[desc]=title.keyword"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.total" should be equal to "4"
    And the JSON node "hits.hits" should have 4 elements
    And the JSON node "hits.hits[0]._source.title" should be equal to "Don't kill Her"
    And the JSON node "hits.hits[1]._source.title" should be equal to "Farming Simulator 18"
    And the JSON node "hits.hits[1]._source.title" should be equal to "Farming Simulator 19"
    And the JSON node "hits.hits[3]._source.title" should be equal to "Persephone"

  Scenario: Games Resource should respond with an error if the direction is incorrect.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&sort[bar]=_score"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.sort" should exist
    And the JSON node "errors.sort[0]" should be equal to 'Provided direction "bar" is not supported. Please use "asc" or "desc".'

  Scenario: Games Resource should respond with an error if the field is not sortable.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&sort[desc]=foo"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.sort" should exist
    And the JSON node "errors.sort[0]" should be equal to 'Provided property "foo" is not sortable.'
