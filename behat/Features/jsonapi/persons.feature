@jsonapi
Feature: Persons

  Scenario: The list of people return only published ones.
    Given I request "/G70VW4Y9sP/jsonapi/node/people"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": "@arrayLength(3)"
      }
      """

  Scenario: Sorting of people listing works.
    Given I request "/G70VW4Y9sP/jsonapi/node/people?sort=-title"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data[0]": {
          "attributes": {
            "title": "Nicolas \"Kaihnn\" Jadaud"
          }
        }
      }
      """
