@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by Release Year a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid year date (YYYY) is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&release_year=2017"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 1 element
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |

    Scenario: Games Resource should respond with an error when a non-valid year date is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&release_year=17"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.releaseYear" should exist
    And the JSON node "errors.releaseYear" should have 1 element
    And the JSON node "errors.releaseYear[0]" should be equal to "This value should be greater than or equal to 1970."
