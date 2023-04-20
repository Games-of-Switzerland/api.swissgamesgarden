@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by Starting/Endinf Release Year a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid starting/ending year date (YYYY) are given.
    When I request "http://api.gos.test/search/games?page=0&release_year_range[start]=2016&release_year_range[end]=2017"
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

  Scenario: Games Resource should respond with filtered games when only a valid starting year date (YYYY) is given.
    When I request "http://api.gos.test/search/games?page=0&release_year_range[start]=2017"
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

  Scenario: Games Resource should respond with filtered games when only a valid ending year date (YYYY) is given.
    When I request "http://api.gos.test/search/games?page=0&release_year_range[end]=2018"
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

  Scenario: Games Resource should respond with an error if the year end is higher than year start.
    When I request "http://api.gos.test/search/games?page=0&release_year_range[start]=2020&release_year_range[end]=2010"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "errors": {"releaseYearRange": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"releaseYearRange[0]": "The start release year can't be higher than the end release year."}
      }
      """

  Scenario: Games Resource should respond with an error with a non-valid surface range key is given.
    When I request "http://api.gos.test/search/games?page=0&release_year_range[test]=2000"
    Then the response code is 400
    And the "Content-Type" response header is "application/json"
    Then the response body contains JSON:
      """
      {
        "errors": {"releaseYearRange": "@arrayLength(1)"}
      }
      """
    Then the response body contains JSON:
      """
      {
        "errors": {"releaseYearRange[0]": "Please provide the start or end release year."}
      }
      """
