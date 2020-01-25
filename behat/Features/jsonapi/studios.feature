Feature: Studio

  Scenario: The list of studio return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/node/studio"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 1 element

  Scenario: Sorting of studio listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/node/studio?sort=-title"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.title" should be equal to "Giants Software"

