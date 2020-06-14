@elasticsearch
  Feature: Retrieve Games Genres facets from Elasticsearch
  In order to filter the Games Search API
  As a client software developer
  I need to be able to fetch faceted Genres in a JSON encoded resources from Elasticsearch via a Proxy

  Scenario: The Genres facets/aggregations should follow a strict given structure.
    Given I send a "GET" request to "http://api.gos.test/search/games?page=0"
    Then the response status code should be 200
    And the response should be in JSON
    Then the JSON should be valid according to the schema "/var/www/behat/Fixtures/elasticsearch/schemaref.search.games.genres.facets.json"

  Scenario Outline: The Genres facets/aggregations should use the same filter as the global query - without itself as filter.
    Given I send a "GET" request to <url>
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets" should have 4 elements
    And the JSON nodes should be equal to:
      | aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets[0].key | Adventure |
      | aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets[0].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets[1].key | Platformer |
      | aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets[1].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets[2].key | Puzzle |
      | aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets[2].doc_count | 1 |
      | aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets[3].key | Simulation |
      | aggregations.aggs_all.all_filtered_genres.all_nested_genres.genres_name_keyword.buckets[3].doc_count | 1 |

    Examples:
      | url |
      | "http://api.gos.test/search/games?page=0" |
      | "http://api.gos.test/search/games?page=0&genresUuid[]=1bf8672b-f341-4287-8aa5-9b16c9131441" |
