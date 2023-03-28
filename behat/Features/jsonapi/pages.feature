@jsonapi
Feature: Pages

  Scenario: The list of pages return only published ones.
    Given I request "/G70VW4Y9sP/jsonapi/node/page"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": "@arrayLength(1)"
      }
      """

  Scenario: Sorting of pages listing works.
    Given I request "/G70VW4Y9sP/jsonapi/node/page?sort=-title"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "title": "About us"
          }
        }
      }
      """
