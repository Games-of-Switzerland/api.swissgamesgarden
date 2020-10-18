Feature: Genre term redirection

  @redirect_disable
  Scenario: Accessing a taxonomy-term Genre page should be forbidden by redirection to frontpage.
    Given I am on "/taxonomy/term/18"
    Then the response status code should be 301
    Then I should be redirected to "/"

