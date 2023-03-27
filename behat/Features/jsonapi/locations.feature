@jsonapi
Feature: Locations

  Scenario: The list of location return only published ones.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/location"
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
              "name": "Geneva"
            }
          },
          {
            "attributes": {
              "name": "Zürich"
            }
          },
          {
            "attributes": {
              "name": "Fribourg"
            }
          },
          {
            "attributes": {
              "name": "Lausanne"
            }
          }
        ]
      }
      """

  Scenario: Sorting of location listing works.
    Given I request "/G70VW4Y9sP/jsonapi/taxonomy_term/location?sort=name"
    Then the response code is 200
    And the "Content-Type" response header is "application/vnd.api+json"
      """
      {
        "data": [
          {
            "attributes": {
              "name": "Fribourg"
            }
          },
          {
            "attributes": {
              "name": "Geneva"
            }
          },
          {
            "attributes": {
              "name": "Lausanne"
            }
          },
          {
            "attributes": {
              "name": "Zürich"
            }
          }
        ]
      }
      """
