@jsonapi
Feature: Location

  Scenario: Fetching a location with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/location/abcdef"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a location by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/location/e181208c-fd4d-47bc-9767-4965d670bc2f"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/location/34"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetch a location using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/location/e181208c-fd4d-47bc-9767-4965d670bc2f"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "name": "Z\u00fcrich",
            "slug": "zurich"
          }
        }
      }
      """

  Scenario: Fetching location using the slug filter may return one or more results.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/location?filter[slug]=zurich"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "langcode": "en",
            "name": "Z\u00fcrich",
            "slug": "zurich"
          }
        }
      }
      """
