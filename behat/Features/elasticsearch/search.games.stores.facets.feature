@elasticsearch
  Feature: Retrieve Games Platforms facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Stores in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Stores facets/aggregations should follow a strict given structure.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON should be valid according to the schema "/var/www/behat/Fixtures/elasticsearch/schemaref.search.games.stores.facets.json"

  Scenario: The Stores facets/aggregations should use the same filter as the global query - without itself as filter.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&stores[]=apple_store"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | f990d6af-d50d-4b35-a79a-72a1e12a7422 |

  Scenario Outline: The Stores facets/aggregations should use the same filter as the global query - without itself as filter.
    Given I send a "GET" request to <url>
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets" should have 4 elements
    And the JSON nodes should be equal to:
      | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[0].key | apple_store |
      | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[0].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[1].key | custom  |
      | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[1].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[2].key | google_play_store |
      | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[2].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[3].key | steam |
      | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[3].doc_count | 1 |
    Examples:
      | url |
      | "http://api.gos.test/search/games?page=0" |
      | "http://api.gos.test/search/games?page=0&stores[]=apple_store" |

    Scenario: The Stores facets/aggregations should be affected by filtered Platforms and/or Genres.
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&platforms[]=pc"
      And the JSON node "aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets" should have 4 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[0].key | custom |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[0].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[1].key | steam  |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[1].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[2].key | apple_store |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[2].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[3].key | google_play_store |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[3].doc_count | 0 |
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&genres[]=puzzle"
      And the JSON node "aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets" should have 4 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[0].key | apple_store |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[0].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[1].key | google_play_store  |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[1].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[2].key | custom |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[2].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[3].key | steam |
        | aggregations.aggs_all.all_filtered_stores.all_nested_stores.stores_name_keyword.buckets[3].doc_count | 0 |
