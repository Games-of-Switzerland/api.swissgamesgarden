@elasticsearch
Feature: Retrieve Autocomplete Wide (People, Studio & Games) items from Elasticsearch
  In order to use an Autocomplete Search API
  As a client software developer
  I need to be able to filter Documents by FullText conditions and retrieve a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: Autocomplete Resource should respond with filtered games/studios/people on matching queries.
    Given I send a "GET" request to "http://api.gos.test/autocomplete?q=giants"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.total" should be equal to "3"
    And the JSON node "aggregations.bundles.bundle.buckets" should have 2 elements
    And the JSON nodes should be equal to:
      | aggregations.bundles.bundle.buckets[0].key | game |
      | aggregations.bundles.bundle.buckets[0].doc_count | 2 |
      | aggregations.bundles.bundle.buckets[1].key | studio |
      | aggregations.bundles.bundle.buckets[1].doc_count | 1 |

  Scenario Outline: Autocomplete Resource should be case-insensitive and ASCII-insensitive.
    Given I send a "GET" request to <url>
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "hits.total" should be equal to "2"
    And the JSON node "aggregations.bundles.bundle.buckets" should have 2 elements
    And the JSON nodes should be equal to:
      | aggregations.bundles.bundle.buckets[0].key | game |
      | aggregations.bundles.bundle.buckets[0].doc_count | 1 |
      | aggregations.bundles.bundle.buckets[1].key | people |
      | aggregations.bundles.bundle.buckets[1].doc_count | 1 |
    And the JSON nodes should be equal to:
      | aggregations.bundles.bundle.buckets[0].top.hits.hits[0]._source.title | Don't kill Her |
      | aggregations.bundles.bundle.buckets[1].top.hits.hits[0]._source.fullname | Jérémy "Wuthrer" Cuany |
    Examples:
      | url |
      | "http://api.gos.test/autocomplete?q=WuthRER" |
      | "http://api.gos.test/autocomplete?q=jérémy" |
