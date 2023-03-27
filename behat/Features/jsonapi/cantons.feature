@jsonapi
Feature: Cantons

  Scenario: The list of canton return only published ones.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/canton"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": "@arrayLength(2)"
      }
      """
    Then the response body contains JSON:
      """
      {
        "data": [
          {
            "attributes": {
              "name": "Vaud"
            }
          },
          {
            "attributes": {
              "name": "Geneva"
            }
          }
        ]
      }
      """

  Scenario: Sorting of canton listing works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/canton?sort=name"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": [
          {
            "attributes": {
              "name": "Geneva"
            }
          },
          {
            "attributes": {
              "name": "Vaud"
            }
          }
        ]
      }
      """
