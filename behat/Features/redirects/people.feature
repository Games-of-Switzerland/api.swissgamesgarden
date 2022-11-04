Feature: People node redirection

  @redirect_disable
  Scenario: Accessing a node People page should permanently redirect you to the Next/React app.
    Given I am on "/node/13"
    Then the response status code should be 301
    Then I should be redirected to "https://swissgames.garden/people/jeremy-wuthrer-cuany"

