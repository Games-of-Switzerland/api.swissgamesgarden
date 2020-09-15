@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by Keywords on Game Title a JSON encoded resources from Elasticsearch via a Proxy

  Scenario Outline: Games Resource should respond with filtered games when a keyword(s) search "q" is given.
    Given I send a "GET" request to <url>
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 1 element
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | 9bb9538f-5b75-4dc0-99b1-ff11d4e2abdd |
      | hits.hits[0]._source.title | Don't kill Her |
    Examples:
      | url |
      | "http://api.gos.test/search/games?page=0&q=kill" |
      | "http://api.gos.test/search/games?page=0&q=KiLL" |
      | "http://api.gos.test/search/games?page=0&q=her" |
      | "http://api.gos.test/search/games?page=0&q=don't%20kill%20her" |

  Scenario: Games Resource should respond with no results when keywords does not match any game.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&q=giants"
    And the JSON node "hits.hits" should have 0 element
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&q=jérémy"
    And the JSON node "hits.hits" should have 0 element
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&q=wuthrer"
    And the JSON node "hits.hits" should have 0 element

  Scenario: Games Resource should respond with filtered games with very accurate precision more keywords we add.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&q=farming"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 2 element
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON node "hits.hits[1]._source" should exist
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |
      | hits.hits[0]._source.title | Farming Simulator 18 |
      | hits.hits[1]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |
      | hits.hits[1]._source.title | Farming Simulator 19 |
