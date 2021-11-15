@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by locations a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid genre slug is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&locations[]=zurich"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 1 element
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |

    Scenario: Games Resource should respond with filtered games when multiple valid locations slugs are given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&locations[]=zurich&locations[]=fribourg"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 2 elements
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |
      | hits.hits[1]._source.uuid | 9bb9538f-5b75-4dc0-99b1-ff11d4e2abdd |

  Scenario: Games Resource should respond with an error when a non-valid genre slug is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&locations[]=test"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.locations" should exist
    And the JSON node "errors.locations" should have 1 element
    And the JSON node "errors.locations[0]" should be equal to "At least one given Location(s) has not been found."
