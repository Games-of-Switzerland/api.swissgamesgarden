Feature: Canton term redirection

  @redirect_disable
  Scenario: Accessing a taxonomy-term Canton page should be forbidden by redirection to frontpage.
    Given I am on "/taxonomy/term/37"
    Then the response status code should be 301
    Then I should be redirected to "/"

