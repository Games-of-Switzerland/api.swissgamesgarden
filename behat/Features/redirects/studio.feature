Feature: Studio node redirection

  @redirect_disable
  Scenario: Accessing a node Studio page should permanently redirect you to the Next/React app.
    Given I am on "/node/15"
    Then the response status code should be 301
    Then I should be redirected to "https://gos.museebolo.ch/studios/giants-software"

