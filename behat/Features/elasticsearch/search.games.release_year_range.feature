@elasticsearch
  Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to filter by Starting/Endinf Release Year a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource should respond with filtered games when a valid starting/ending year date (YYYY) are given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&release_year_range[start]=2016&release_year_range[end]=2017"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 1 element
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |

  Scenario: Games Resource should respond with filtered games when only a valid starting year date (YYYY) is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&release_year_range[start]=2017"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 2 element
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON node "hits.hits[1]._source" should exist
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |
      | hits.hits[1]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |

  Scenario: Games Resource should respond with filtered games when only a valid ending year date (YYYY) is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&release_year_range[end]=2018"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 2 elements
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON node "hits.hits[1]._source" should exist
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |
      | hits.hits[1]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |

  Scenario: Games Resource should respond with an error if the year end is higher than year start.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&release_year_range[start]=2020&release_year_range[end]=2010"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.releaseYearRange" should exist
    And the JSON node "errors.releaseYearRange[0]" should be equal to "The start release year can't be higher than the end release year."

  Scenario: Games Resource should respond with an error with a non-valid surface range key is given.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&release_year_range[test]=2000"
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.releaseYearRange" should exist
    And the JSON node "errors.releaseYearRange[0]" should be equal to "Please provide the start or end release year."
