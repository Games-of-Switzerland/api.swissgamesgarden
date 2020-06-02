@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by platforms a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid platform UUID is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&platformsUuid[]=304a43fe-3c4d-4587-93e6-a84959d39bf7"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 2 element
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |
      | hits.hits[0]._source.id | 11 |
      | hits.hits[1]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |
      | hits.hits[1]._source.id | 12 |

  Scenario: Games Resource should respond with filtered games when multiple valid platforms UUID are given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&platformsUuid[]=304a43fe-3c4d-4587-93e6-a84959d39bf7&platformsUuid[]=6ea716ae-e50f-4a59-ace5-603c353ae20a"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 3 elements
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |
      | hits.hits[0]._source.id | 11 |
      | hits.hits[1]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |
      | hits.hits[1]._source.id | 12 |
      | hits.hits[2]._source.uuid | f990d6af-d50d-4b35-a79a-72a1e12a7422 |
      | hits.hits[2]._source.id | 17 |

  Scenario: Games Resource should respond with an error when a non-valid platform UUID is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&platformsUuid[]=test"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.platforms" should exist
    And the JSON node "errors.platforms" should have 1 element
    And the JSON node "errors.platforms[0]" should be equal to "At least one given Platform(s) UUID has not been found."
