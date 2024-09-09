Feature: Login

  @api
  Scenario: Login works
    Given I am logged in as a user with the "administrator" role
    And the url should match "/admin/content"
    And the response status code should be 200

  @api
  Scenario: Login show message(s) on error & forget password link
    Given I am on "/user/login"
    When I fill in "edit-name" with "Batman"
    Then I fill in "edit-pass" with "RobinMyLove"
    And I press "edit-submit"
    And I should see "Unrecognized username or password. Forgot your password?" in the "div[role=contentinfo]" element
    And the response status code should be 200
