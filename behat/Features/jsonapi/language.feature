@jsonapi
Feature: Language

  Scenario: Fetching a language with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/language/abcdef"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a language by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/language/c748e76c-4a40-4b04-9e69-17ea602cb0b0"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/language/29"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetch a language using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/language/c748e76c-4a40-4b04-9e69-17ea602cb0b0"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "name": "English"
          }
        }
      }
      """
