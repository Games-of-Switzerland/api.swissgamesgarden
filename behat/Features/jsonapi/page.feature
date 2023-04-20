@jsonapi
Feature: Page

  Scenario: Fetching a page with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/node/page/abcdef"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a page by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/node/page/38e10164-166d-4c76-be68-0a3a48cf2966"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/node/page/18"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetch a page using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/node/page/38e10164-166d-4c76-be68-0a3a48cf2966"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "title": "About us",
            "field_path": "/pages/about-us"
          }
        }
      }
      """

  Scenario: Fetching page using the path filter may return one or more results.
    Given I request "/G70VW4Y9sP/jsonapi/node/page?filter[field_path]=/pages/about-us"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "langcode": "en",
            "title": "About us",
            "field_path": "/pages/about-us"
          }
        }
      }
      """
