@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by Keywords on Game Title a JSON encoded resources from Elasticsearch via a Proxy

  Scenario Outline: Games Resource should respond with filtered games when a keyword(s) search "q" is given.
    When I request <url>
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {"hits": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"uuid": "9bb9538f-5b75-4dc0-99b1-ff11d4e2abdd"}
          }
        }
      }
      """

    Examples:
      | url |
      | "http://api.gos.test/search/games?page=0&q=kill" |
      | "http://api.gos.test/search/games?page=0&q=KiLL" |
      | "http://api.gos.test/search/games?page=0&q=her" |
      | "http://api.gos.test/search/games?page=0&q=don't%20kill%20her" |

  Scenario: Games Resource should respond with no results when keywords does not match any game.
    When I request "http://api.gos.test/search/games?page=0&q=giants"
    Then the response body contains JSON:
      """
      {
        "hits": {"hits": "@arrayLength(0)"}
      }
      """
    When I request "http://api.gos.test/search/games?page=0&q=jérémy"
    Then the response body contains JSON:
      """
      {
        "hits": {"hits": "@arrayLength(0)"}
      }
      """
    When I request "http://api.gos.test/search/games?page=0&q=wuthrer"
    Then the response body contains JSON:
      """
      {
        "hits": {"hits": "@arrayLength(0)"}
      }
      """

  Scenario: Games Resource should respond with filtered games with very accurate precision more keywords we add.
    When I request "http://api.gos.test/search/games?page=0&q=farming"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {"hits": "@arrayLength(2)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"uuid": "08952aa6-e079-496a-8efa-cbb8465d9315"}
          },
          "hits[1]": {
            "_source": {"uuid": "a0b7c853-c891-487f-84f9-74dfbce9fa63"}
          }
        }
      }
      """
