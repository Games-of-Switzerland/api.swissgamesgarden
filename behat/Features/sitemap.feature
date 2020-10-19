@sitemap
Feature: Sitemap

  Scenario: The Sitemap is accessible by anonymous
    Given I am on "/sitemap.xml"
    And the response status code should be 200

  Scenario: The Sitemap contain the proper pages and not everything
    Given the sitemap "/sitemap.xml"
    Then the sitemap should have 8 children

  Scenario: The Sitemap contain absolute URL pointing to React/Next App.
    Given I am on "/sitemap.xml"
    Then the XML element "//url[1]/loc" should contain "https://gos.museebolo.ch/games/dont-kill-her"
    Then the XML element "//url[5]/loc" should contain "https://gos.museebolo.ch/people/jeremy-wuthrer-cuany"
    Then the XML element "//url[8]/loc" should contain "https://gos.museebolo.ch/studios/giants-software"
