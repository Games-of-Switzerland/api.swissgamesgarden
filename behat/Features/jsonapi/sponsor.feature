@jsonapi
Feature: Sponsor

  Scenario: Fetching a sponsor with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor/abcdef"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a sponsor by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor/dc78845d-ba97-4fe3-889b-30680eedcd91"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor/26"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetch a sponsor using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor/dc78845d-ba97-4fe3-889b-30680eedcd91"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "name": "Kickstarter",
            "field_path": "/sponsors/kickstarter"
          }
        }
      }
      """

  Scenario: Fetching sponsor using the path filter may return one or more results.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor?filter[field_path]=/sponsors/kickstarter"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "langcode": "en",
            "name": "Kickstarter",
            "field_path": "/sponsors/kickstarter"
          }
        }
      }
      """
