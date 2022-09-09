Feature: Game node redirection

  @redirect_disable
  Scenario: Accessing a node Game page should permanently redirect you to the Next/React app.
    Given I am on "/node/12"
    Then the response status code should be 301
    Then I should be redirected to "https://swissgames.garden/games/farming-simulator-19"

