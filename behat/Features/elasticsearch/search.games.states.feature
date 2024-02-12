@elasticsearch
Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by states a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid store name is given.
    When I request "http://api.gos.test/search/games?page=0&states[]=pre_release"
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
            "_source": {"uuid": "a0b7c853-c891-487f-84f9-74dfbce9fa63"}
          }
        }
      }
      """

  Scenario: Games Resource should respond with filtered games when multiple valid states names are given.
    When I request "http://api.gos.test/search/games?page=0&states[]=released&states[]=pre_release"
    Then the response code is 200
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "hits": {"hits": "@arrayLength(3)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "hits": {
          "hits[0]": {
            "_source": {"uuid": "f990d6af-d50d-4b35-a79a-72a1e12a7422"}
          },
          "hits[1]": {
            "_source": {"uuid": "a0b7c853-c891-487f-84f9-74dfbce9fa63"}
          },
          "hits[2]": {
            "_source": {"uuid": "08952aa6-e079-496a-8efa-cbb8465d9315"}
          }
        }
      }
      """

  Scenario: Games Resource should respond with an error when a non-valid store name is given.
    When I request "http://api.gos.test/search/games?page=0&states[]=foo"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "errors": {"states": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"states[0]": "One or more of the given values is invalid."}
      }
      """
