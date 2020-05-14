@elasticsearch
Feature: Retrieve Games items from Elasticsearch
  In order to use a Games Search API
  As a client software developer
  I need to be able to retrieve JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Games Resource without search parameter return all gmes.
    Given I send a "GET" request to "http://api.gos.test/search/games"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "hits.total" should be equal to "4"
    And the JSON node "hits.hits" should have 4 element
