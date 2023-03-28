@jsonapi
Feature: Languages

  Scenario: The list of language return only published ones.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/language"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": "@arrayLength(4)"
      }
      """
    Then the response body contains JSON:
      """
      {
        "data": [
          {
            "attributes": {
              "name": "English"
            }
          },
          {
            "attributes": {
              "name": "French"
            }
          },
          {
            "attributes": {
              "name": "German"
            }
          },
          {
            "attributes": {
              "name": "Spanish"
            }
          }
        ]
      }
      """

  Scenario: Sorting of language listing works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/language?sort=-name"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": [
          {
            "attributes": {
              "name": "Spanish"
            }
          },
          {
            "attributes": {
              "name": "German"
            }
          },
          {
            "attributes": {
              "name": "French"
            }
          },
          {
            "attributes": {
              "name": "English"
            }
          }
        ]
      }
      """
