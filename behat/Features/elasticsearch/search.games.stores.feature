@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by stores a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid store name is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&stores[]=steam"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 1 element
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |

  Scenario: Games Resource should respond with filtered games when multiple valid stores names are given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&stores[]=apple_store&stores[]=google_play_store"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 1 element
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | f990d6af-d50d-4b35-a79a-72a1e12a7422 |

  Scenario: Games Resource should respond with an error when a non-valid store name is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&stores[]=foo"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.stores" should exist
    And the JSON node "errors.stores" should have 1 element
    And the JSON node "errors.stores[0]" should be equal to "One or more of the given values is invalid."
