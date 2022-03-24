@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by cantons a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid canton slug is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&cantons[]=vaud"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 1 element
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | f990d6af-d50d-4b35-a79a-72a1e12a7422 |

    Scenario: Games Resource should respond with filtered games when multiple valid cantons slugs are given.
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&cantons[]=geneva&cantons[]=vaud"
      Then the response status code should be 200
      And the response should be in JSON
      And the JSON node "hits.hits" should have 2 elements
      And the JSON node "hits.hits[0]._source" should exist
      And the JSON node "hits.hits[1]._source" should exist
      And the JSON nodes should be equal to:
        | hits.hits[0]._source.uuid | 9bb9538f-5b75-4dc0-99b1-ff11d4e2abdd |
        | hits.hits[1]._source.uuid | f990d6af-d50d-4b35-a79a-72a1e12a7422 |

  Scenario: Games Resource should respond with an error when a non-valid canton slug is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&cantons[]=test"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.cantons" should exist
    And the JSON node "errors.cantons" should have 1 element
    And the JSON node "errors.cantons[0]" should be equal to "At least one given Canton(s) has not been found."
