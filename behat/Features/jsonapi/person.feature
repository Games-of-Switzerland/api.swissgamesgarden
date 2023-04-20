@jsonapi
Feature: Person

  Scenario: Fetching a person with a wrong UUID should return a 404.
    Given I request "/G70VW4Y9sP/jsonapi/node/people/abcdef"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetching a person by his UUID works, fetching it by his NID does not works.
    Given I request "/G70VW4Y9sP/jsonapi/node/people/c2c1d560-d9f4-4878-8105-7f63ec09e7ef"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Given I request "/G70VW4Y9sP/jsonapi/node/people/1"
    Then the response code is 404
    And the "Content-Type" response header is "application/vnd.api+json"

  Scenario: Fetch a person using UUID should return it in the default language
    Given I request "/G70VW4Y9sP/jsonapi/node/people/c2c1d560-d9f4-4878-8105-7f63ec09e7ef"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": {
          "attributes": {
            "langcode": "en",
            "title": "Jérémy \"Wuthrer\" Cuany",
            "field_path": "/people/jeremy-wuthrer-cuany"
          }
        }
      }
      """

  Scenario: Fetching people using the path filter may return one or more results.
    Given I request "/G70VW4Y9sP/jsonapi/node/people?filter[field_path]=/people/jeremy-wuthrer-cuany"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "langcode": "en",
            "title": "Jérémy \"Wuthrer\" Cuany",
            "field_path": "/people/jeremy-wuthrer-cuany"
          }
        }
      }
      """
