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

  Scenario: Games Resource should respond with specific Elasticsearch structure.
    Given I send a "GET" request to "http://api.gos.test/search/games"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.hits" should have 4 elements
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON node "hits.hits[0]._source" should have 7 elements
    And the JSON node "hits.hits[0]._source.uuid" should exist
    And the JSON node "hits.hits[0]._source.title" should exist
    And the JSON node "hits.hits[0]._source.desc" should exist
    And the JSON node "hits.hits[0]._source.is_published" should exist
    And the JSON node "hits.hits[0]._source.id" should exist
    And the JSON node "hits.hits[0]._source.releases" should exist
    And the JSON node "hits.hits[0]._source.releases" should have 6 elements
    And the JSON node "hits.hits[0]._source.releases[0]" should have 4 elements
    And the JSON node "hits.hits[0]._source.releases[0].date" should exist
    And the JSON node "hits.hits[0]._source.releases[0].platform" should exist
    And the JSON node "hits.hits[0]._source.releases[0].platform_keyword" should exist
    And the JSON node "hits.hits[0]._source.releases[0].platform_uuid" should exist
    And the JSON node "hits.hits[0]._source.studios" should exist
    And the JSON node "hits.hits[0]._source.studios" should have 1 element
    And the JSON node "hits.hits[0]._source.studios[0]" should have 2 elements
    And the JSON node "hits.hits[0]._source.studios[0].name" should exist
    And the JSON node "hits.hits[0]._source.studios[0].uuid" should exist
    And the JSON node "hits.hits[0]._source.genres" should not exist
    And the JSON node "hits.hits[0]._source" should exist
    And the JSON node "hits.hits[1]._source" should have 8 elements
    And the JSON node "hits.hits[1]._source.uuid" should exist
    And the JSON node "hits.hits[1]._source.title" should exist
    And the JSON node "hits.hits[1]._source.desc" should exist
    And the JSON node "hits.hits[1]._source.is_published" should exist
    And the JSON node "hits.hits[1]._source.id" should exist
    And the JSON node "hits.hits[1]._source.releases" should exist
    And the JSON node "hits.hits[1]._source.releases" should have 4 elements
    And the JSON node "hits.hits[1]._source.releases[0]" should have 4 elements
    And the JSON node "hits.hits[1]._source.releases[0].date" should exist
    And the JSON node "hits.hits[1]._source.releases[0].platform" should exist
    And the JSON node "hits.hits[1]._source.releases[0].platform_keyword" should exist
    And the JSON node "hits.hits[1]._source.releases[0].platform_uuid" should exist
    And the JSON node "hits.hits[1]._source.studios" should exist
    And the JSON node "hits.hits[1]._source.studios" should have 1 element
    And the JSON node "hits.hits[1]._source.studios[0]" should have 2 elements
    And the JSON node "hits.hits[1]._source.studios[0].name" should exist
    And the JSON node "hits.hits[1]._source.studios[0].uuid" should exist
    And the JSON node "hits.hits[1]._source.genres" should exist
    And the JSON node "hits.hits[1]._source.genres" should have 1 element
    And the JSON node "hits.hits[1]._source.genres[0]" should have 3 elements
    And the JSON node "hits.hits[1]._source.genres[0].name" should exist
    And the JSON node "hits.hits[1]._source.genres[0].name_keyword" should exist
    And the JSON node "hits.hits[1]._source.genres[0].uuid" should exist
