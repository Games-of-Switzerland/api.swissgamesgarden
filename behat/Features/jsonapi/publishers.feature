@jsonapi
Feature: Publishers

  Scenario: The list of publisher return only published ones.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher"
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
        "data": [
          {
            "attributes": {
              "name": "Shy Robot Games"
            }
          },
          {
            "attributes": {
              "name": "Astragon"
            }
          },
          {
            "attributes": {
              "name": "Focus Home Interactive"
            }
          }
        ]
      }
      """

  Scenario: Sorting of publisher listing works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/publisher?sort=name"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
    Then the response body contains JSON:
      """
      {
        "data": [
          {
            "attributes": {
              "name": "Astragon"
            }
          },
          {
            "attributes": {
              "name": "Focus Home Interactive"
            }
          },
          {
            "attributes": {
              "name": "Shy Robot Games"
            }
          }
        ]
      }
      """
