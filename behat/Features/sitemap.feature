@sitemap
Feature: Sitemap

  Scenario: The Sitemap is accessible by anonymous
    Given I am on "/sitemap.xml"
    And the response status code should be 200

  Scenario: The Sitemap contain the proper pages and not everything
    Given the sitemap "/sitemap.xml"
    Then the sitemap should have 4 children
