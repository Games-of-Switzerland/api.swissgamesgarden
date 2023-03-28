@jsonapi
Feature: Sponsors

  Scenario: The list of sponsor return only published ones.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": "@arrayLength(3)"
      }
      """
    Then the response body contains JSON:
      """
      {
        "data":[
          {
            "attributes": {
              "name": "Kickstarter"
            }
          },
          {
            "attributes": {
              "name": "Numerik Games Festival"
            }
          },
          {
            "attributes": {
              "name": "Alimentarium"
            }
          }
        ]
      }
      """

  Scenario: Sorting of sponsor listing works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/sponsor?sort=name"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data":[
          {
            "attributes": {
              "name": "Alimentarium"
            }
          },
          {
            "attributes": {
              "name": "Kickstarter"
            }
          },
          {
            "attributes": {
              "name": "Numerik Games Festival"
            }
          }
        ]
      }
      """
