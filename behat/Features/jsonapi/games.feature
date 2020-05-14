Feature: Agents

  Scenario: The list of games return only published ones.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data" should have 4 elements

  Scenario: Sorting of games listing works.
    Given I am on "/G70VW4Y9sP/jsonapi/node/game?sort=-title"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "data[0].attributes.title" should be equal to "Persephone"
