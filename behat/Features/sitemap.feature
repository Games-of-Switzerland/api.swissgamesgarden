@sitemap
Feature: Sitemap

  Scenario: The Sitemap is accessible by anonymous
    Given I am on "/sitemap.xml"
    And the response status code should be 200

  Scenario: The Sitemap contain the proper pages and not everything
    Given the sitemap "/sitemap.xml"
    Then the sitemap should have 10 children

  Scenario: The Sitemap contain absolute URL pointing to React/Next App.
    Given I am on "/sitemap.xml"
    Then the response should contain "https://swissgames.garden/"

  Scenario: The Sitemap contains Games with adapted scoring and change frequency.
    Given I am on "/sitemap.xml"
    Then the XML element "//url[1]/loc" should contain "https://swissgames.garden/games/dont-kill-her"
    Then the XML element "//url[1]/changefreq" should contain "monthly"
    Then the XML element "//url[1]/priority" should contain "1.0"

  Scenario: The Sitemap contains Basic Pages with adapted scoring and change frequency.
    Given I am on "/sitemap.xml"
    Then the XML element "//url[5]/loc" should contain "https://swissgames.garden/pages/about-us"
    Then the XML element "//url[5]/changefreq" should contain "yearly"
    Then the XML element "//url[5]/priority" should contain "0.7"

  Scenario: The Sitemap contains People with adapted scoring and change frequency.
    Given I am on "/sitemap.xml"
    Then the XML element "//url[6]/loc" should contain "https://swissgames.garden/people/jeremy-wuthrer-cuany"
    Then the XML element "//url[6]/changefreq" should contain "monthly"
    Then the XML element "//url[6]/priority" should contain "0.5"

  Scenario: The Sitemap contains Studios with adapted scoring and change frequency.
    Given I am on "/sitemap.xml"
    Then the XML element "//url[9]/loc" should contain "https://swissgames.garden/studios/giants-software"
    Then the XML element "//url[9]/changefreq" should contain "monthly"
    Then the XML element "//url[9]/priority" should contain "0.5"

  Scenario: The Sitemap contains the homepage with adapted scoring and change frequency.
    Given I am on "/sitemap.xml"
    Then the XML element "//url[10]/loc" should contain "https://swissgames.garden/"
    Then the XML element "//url[10]/changefreq" should contain "always"
    Then the XML element "//url[10]/priority" should contain "1.0"
