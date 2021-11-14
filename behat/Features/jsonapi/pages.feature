Feature: Pages

  Scenario: The list of pages return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/node/page"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 1 element

  Scenario: Sorting of pages listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/node/page?sort=-title"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.title" should be equal to "About us"
