@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by platforms a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid platform slug is given.
    When I request "http://api.gos.test/search/games?page=0&platforms[]=pc"
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
            "_source": {"uuid": "a0b7c853-c891-487f-84f9-74dfbce9fa63"}
          },
          "hits[1]": {
            "_source": {"uuid": "08952aa6-e079-496a-8efa-cbb8465d9315"}
          }
        }
      }
      """

  Scenario: Games Resource should respond with filtered games when multiple valid platforms slugs are given.
    When I request "http://api.gos.test/search/games?page=0&platforms[]=pc&platforms[]=ios"
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
            "_source": {"uuid": "a0b7c853-c891-487f-84f9-74dfbce9fa63"}
          },
          "hits[1]": {
            "_source": {"uuid": "08952aa6-e079-496a-8efa-cbb8465d9315"}
          },
          "hits[2]": {
            "_source": {"uuid": "f990d6af-d50d-4b35-a79a-72a1e12a7422"}
          }
        }
      }
      """

  Scenario: Games Resource should respond with an error when a non-valid platform slug is given.
    When I request "http://api.gos.test/search/games?page=0&platforms[]=test"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "errors": {"platforms": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"platforms[0]": "At least one given Platform(s) has not been found."}
      }
      """
