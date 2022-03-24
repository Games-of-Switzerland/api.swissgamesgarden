@elasticsearch
  Feature: Retrieve Games Cantons facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Cantons in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Cantons facets/aggregations should follow a strict given structure.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON should be valid according to the schema "/var/www/behat/Fixtures/elasticsearch/schemaref.search.games.cantons.facets.json"

  Scenario Outline: The Cantons facets/aggregations should use the same filter as the global query - without itself as filter.
    Given I send a "GET" request to <url>
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets" should have 2 elements
    And the JSON nodes should be equal to:
      | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[0].key | geneva |
      | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[0].doc_count | 2 |
      | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[1].key | vaud |
      | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[1].doc_count | 1 |
    Examples:
      | url |
      | "http://api.gos.test/search/games?page=0" |
      | "http://api.gos.test/search/games?page=0&cantons[]=geneva" |

    Scenario: The Cantons facets/aggregations should be affected by filtered Stores and/or Platforms.
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&platforms[]=ios"
    And the JSON node "aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets" should have 2 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[0].key | geneva |
        | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[0].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[1].key | vaud |
        | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[1].doc_count | 1 |

      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&stores[]=steam"
    And the JSON node "aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets" should have 2 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[0].key | geneva |
        | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[0].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[1].key | vaud |
        | aggregations.aggs_all.all_filtered_cantons.all_nested_cantons.cantons_name_keyword.buckets[1].doc_count | 0 |
