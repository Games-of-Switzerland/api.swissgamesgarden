Feature: Sponsor term redirection

  @redirect_disable
  Scenario: Accessing a taxonomy-term Sponsor page should be forbidden by redirection to frontpage.
    Given I am on "/taxonomy/term/28"
    Then the response status code should be 301
    Then I should be redirected to "/"
