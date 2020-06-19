@elasticsearch
  Feature: Retrieve Games Locations facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Locations in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Locations facets/aggregations should follow a strict given structure.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON should be valid according to the schema "/var/www/behat/Fixtures/elasticsearch/schemaref.search.games.locations.facets.json"

  Scenario Outline: The Locations facets/aggregations should use the same filter as the global query - without itself as filter.
    Given I send a "GET" request to <url>
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets" should have 2 elements
    And the JSON nodes should be equal to:
      | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[0].key | fribourg |
      | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[0].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[1].key | zurich |
      | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[1].doc_count | 1 |
    Examples:
      | url |
      | "http://api.gos.test/search/games?page=0" |
      | "http://api.gos.test/search/games?page=0&locations[]=zurich" |

    Scenario: The Locations facets/aggregations should be affected by filtered Stores and/or Platforms.
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&platforms[]=ios"
    And the JSON node "aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets" should have 2 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[0].key | fribourg |
        | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[0].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[1].key | zurich |
        | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[1].doc_count | 0 |
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&stores[]=steam"
    And the JSON node "aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets" should have 2 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[0].key | zurich |
        | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[0].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[1].key | fribourg |
        | aggregations.aggs_all.all_filtered_locations.all_nested_locations.locations_name_keyword.buckets[1].doc_count | 0 |
