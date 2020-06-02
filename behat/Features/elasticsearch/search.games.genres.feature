@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by genres a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid genre UUID is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&genresUuid[]=1bf8672b-f341-4287-8aa5-9b16c9131441"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 1 element
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |
      | hits.hits[0]._source.id | 12 |

  Scenario: Games Resource should respond with filtered games when multiple valid genres UUID are given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&genresUuid[]=1bf8672b-f341-4287-8aa5-9b16c9131441&genresUuid[]=55d245b5-c9cc-43e5-8400-c44ef4f2d8ad"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 2 elements
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |
      | hits.hits[0]._source.id | 12 |
      | hits.hits[1]._source.uuid | f990d6af-d50d-4b35-a79a-72a1e12a7422 |
      | hits.hits[1]._source.id | 17 |

  Scenario: Games Resource should respond with an error when a non-valid genre UUID is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&genresUuid[]=test"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.genres" should exist
    And the JSON node "errors.genres" should have 1 element
    And the JSON node "errors.genres[0]" should be equal to "At least one given Genre(s) UUID has not been found."
