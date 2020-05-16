@elasticsearch
Feature: Retrieve Autocomplete Wide (People, Studio & Games) items from Elasticsearch
  In order to use an Autocomplete Search API
  As a client software developer
  I need to be able to retrieve JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Autocomplete Resource without search parameter return as most Games, People and Studio as the limit-per-bundle allowed (5).
    Given I send a "GET" request to "http://api.gos.test/autocomplete"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should be equal to "application/json"
    And the JSON node "hits.total" should be equal to "8"

  Scenario: Autocomplete Resource should respond with specific Elasticsearch structure.
    Given I send a "GET" request to "http://api.gos.test/autocomplete"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON should be valid according to the schema "/var/www/behat/Fixtures/elasticsearch/schemaref.autocomplete.wide.json"
