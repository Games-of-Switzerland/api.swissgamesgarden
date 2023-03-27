@jsonapi
Feature: Location

  Scenario: Fetching a canton with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/canton/abcdef"
    Then the response code is 404
    Then the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a canton by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/canton/7eb54de6-4f2c-471e-b84b-ac7a9c8ff729"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/canton/37"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetch a canton using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/canton/7eb54de6-4f2c-471e-b84b-ac7a9c8ff729"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "name": "Vaud",
            "slug": "vaud"
          }
        }
      }
      """

  Scenario: Fetching canton using the slug filter may return one or more results.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/canton?filter[slug]=vaud"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "langcode": "en",
            "name": "Vaud",
            "slug": "vaud"
          }
        }
      }
      """
