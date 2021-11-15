@elasticsearch
  Feature: Retrieve Games Platforms facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Platforms in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Platforms facets/aggregations should follow a strict given structure.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON should be valid according to the schema "/var/www/behat/Fixtures/elasticsearch/schemaref.search.games.platforms.facets.json"

  Scenario: The Platforms facets/aggregations should use the same filter as the global query - without itself as filter.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0&platforms[]=pc"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON nodes should be equal to:
      | hits.hits[0]._source.uuid | a0b7c853-c891-487f-84f9-74dfbce9fa63 |
      | hits.hits[1]._source.uuid | 08952aa6-e079-496a-8efa-cbb8465d9315 |

  Scenario Outline: The Platforms facets/aggregations should use the same filter as the global query - without itself as filter.
    Given I send a "GET" request to <url>
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets" should have 8 elements
    And the JSON nodes should be equal to:
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[0].key | ios |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[0].doc_count | 2 |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[1].key | mac |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[1].doc_count | 2 |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[2].key | pc |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[2].doc_count | 2 |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[3].key | android |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[3].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[4].key | nintendo_3ds |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[4].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[5].key | playstation_4 |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[5].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[6].key | playstation_vita |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[6].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[7].key | xbox_one |
      | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[7].doc_count | 1 |
    Examples:
      | url |
      | "http://api.gos.test/search/games?page=0" |
      | "http://api.gos.test/search/games?page=0&platforms[]=pc" |

    Scenario: The Platforms facets/aggregations should be affected by filtered Stores and/or Genres.
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&genres[]=puzzle"
      And the JSON node "aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets" should have 8 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[0].key | ios |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[0].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[1].key | android |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[1].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[2].key | mac |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[2].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[3].key | nintendo_3ds |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[3].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[4].key | pc |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[4].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[5].key | playstation_4 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[5].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[6].key | playstation_vita |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[6].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[7].key | xbox_one |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[7].doc_count | 0 |
      Given I send a "GET" request to "http://api.gos.test/search/games?page=0&stores[]=steam"
      And the JSON node "aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets" should have 8 elements
      And the JSON nodes should be equal to:
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[0].key | mac |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[0].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[1].key | pc |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[1].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[2].key | playstation_4 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[2].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[3].key | xbox_one |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[3].doc_count | 1 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[4].key | android |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[4].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[5].key | ios |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[5].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[6].key | nintendo_3ds |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[6].doc_count | 0 |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[7].key | playstation_vita |
        | aggregations.aggs_all.all_filtered_platforms.all_nested_platforms.platforms_name_keyword.buckets[7].doc_count | 0 |
