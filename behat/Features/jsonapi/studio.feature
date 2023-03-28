@jsonapi
Feature: Studio

  Scenario: Fetching a studio with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/node/studio/abcdef"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a studio by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/node/studio/b7e315a4-65a1-4a34-b989-e2a5a96e1f22"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/node/studio/1"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetch a studio using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/node/studio/b7e315a4-65a1-4a34-b989-e2a5a96e1f22"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "title": "Giants Software",
            "field_path": "/studios/giants-software"
          }
        }
      }
      """

  Scenario: Fetching studio using the path filter may return one or more results.
    Given I request "/G70VW4Y9sP/jsonapi/node/studio?filter[field_path]=/studios/giants-software"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "langcode": "en",
            "title": "Giants Software",
            "field_path": "/studios/giants-software"
          }
        }
      }
      """
