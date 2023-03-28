@jsonapi
Feature: Games

  Scenario: The list of games return only published ones.
    Given I request "/G70VW4Y9sP/jsonapi/node/game"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": "@arrayLength(4)"
      }
      """

  Scenario: Sorting of games listing works.
    Given I request "/G70VW4Y9sP/jsonapi/node/game?sort=-title"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": [
          {
            "attributes": {
              "title": "Persephone"
            }
          }
        ]
      }
      """
