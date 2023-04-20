@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by locations a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid genre slug is given.
    When I request "http://api.gos.test/search/games?page=0&locations[]=zurich"
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
            "_source": {"uuid": "08952aa6-e079-496a-8efa-cbb8465d9315"}
          }
        }
      }
      """

    Scenario: Games Resource should respond with filtered games when multiple valid locations slugs are given.
    When I request "http://api.gos.test/search/games?page=0&locations[]=zurich&locations[]=fribourg"
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
            "_source": {"uuid": "08952aa6-e079-496a-8efa-cbb8465d9315"}
          }
        }
      }
      """

  Scenario: Games Resource should respond with an error when a non-valid genre slug is given.
    When I request "http://api.gos.test/search/games?page=0&locations[]=test"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "errors": {"locations": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"locations[0]": "At least one given Location(s) has not been found."}
      }
      """
