@jsonapi
Feature: Publisher

  Scenario: Fetching a publisher with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher/abcdef"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a publisher by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher/c16c1119-ae04-460c-9fed-e9e20d5785e7"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher/23"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetch a publisher using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher/c16c1119-ae04-460c-9fed-e9e20d5785e7"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "name": "Shy Robot Games",
            "field_path": "/publishers/shy-robot-games"
          }
        }
      }
      """

  Scenario: Fetching publisher using the path filter may return one or more results.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher?filter[field_path]=/publishers/shy-robot-games"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "langcode": "en",
            "name": "Shy Robot Games",
            "field_path": "/publishers/shy-robot-games"
          }
        }
      }
      """
