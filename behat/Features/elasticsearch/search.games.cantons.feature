@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by cantons a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid canton slug is given.
    When I request "http://api.gos.test/search/games?page=0&cantons[]=vaud"
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
            "_source": {"uuid": "f990d6af-d50d-4b35-a79a-72a1e12a7422"}
          }
        }
      }
      """

    Scenario: Games Resource should respond with filtered games when multiple valid cantons slugs are given.
      When I request "http://api.gos.test/search/games?page=0&cantons[]=geneva&cantons[]=vaud&sort[asc]=title.keyword"
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
            "_source": {"uuid": "9bb9538f-5b75-4dc0-99b1-ff11d4e2abdd"}
          },
          "hits[1]": {
            "_source": {"uuid": "f990d6af-d50d-4b35-a79a-72a1e12a7422"}
          }
        }
      }
      """

  Scenario: Games Resource should respond with an error when a non-valid canton slug is given.
    When I request "http://api.gos.test/search/games?page=0&cantons[]=test"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "errors": {"cantons": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"cantons[0]": "At least one given Canton(s) has not been found."}
      }
      """
