<?php

// phpcs:ignoreFile

$databases['default']['default'] = array (
  'database' => 'drupal_development',
  'username' => 'drupal',
  'password' => 'drupal',
  'prefix' => '',
  'host' => 'db',
  'port' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 */
$settings['hash_salt'] = 'O8PdFrl8MAcMZcxhCKVU-Gtz5uX0ztAGiuKej3mLEUkjtoOnVrl2T1XIqXVdeegbgt57ZX_TPg';

$config['backerymails.settings']['reroute']['status'] = FALSE;
$config['backerymails.settings']['reroute']['recipients'] = 'kevin@antistatique.net';

/**
 * Setting used to add a prefix for ES index based on the environment.
 */
$settings['gos_elasticsearch.index_prefix'] = 'development';

/**
 * The CDN static-content status.
 *
 * @var boolean
 */
$config['cdn.settings']['status'] = false;

/**
 * The elasticsearch host.
 *
 * @var string
 */
$config['elasticsearch_helper.settings']['elasticsearch_helper']['host'] = 'elasticsearch';
$config['elasticsearch_helper.settings']['elasticsearch_helper']['port'] = '9200';

/**
 * Base URL of the Next App.
 *
 * This value should not contain a leading slash (/).
 */
$config['frontend']['base_url'] = 'https://gos.museebolo.ch';

/**
 * The base URL of sitemap links can be overridden here.
 *
 * @var string
 */
$config['simple_sitemap.settings']['base_url'] = 'http://api.gos.test';

/**
 * Swiftmailer transport using mailcatcher.
 */
$config['swiftmailer.transport']['transport'] = 'smtp';
$config['swiftmailer.transport']['smtp_host'] = 'localhost';
$config['swiftmailer.transport']['smtp_port'] = '1025';
$config['swiftmailer.transport']['smtp_encryption'] = '0';

/**
 * Private file path.
 *
 * @var string
 */
$settings['file_private_path'] = '/var/www/web/sites/default/files/private';

/**
 * Increase memory limit of CLI for ES indexing.
 */
if (PHP_SAPI === 'cli') {
  ini_set('memory_limit', '2G');
}

/**
 * Disable psalm annotation as we can't update to doctrine/annotations:^1.6.1.
 *
 * @see https://github.com/doctrine/collections/issues/198
 */
//\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('psalm');

/**
 * Drupal Configuration for development.
 *
 * Disable all caches capabilities.
 */
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$config['system.logging']['error_level'] = 'all';
$config['system.performance']['cache']['page']['use_internal'] = FALSE;
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['css']['gzip'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$config['system.performance']['js']['gzip'] = FALSE;
$config['system.performance']['response']['gzip'] = FALSE;
$config['views.settings']['ui']['show']['sql_query']['enabled'] = TRUE;
$config['views.settings']['ui']['show']['performance_statistics'] = TRUE;
$config['devel.settings']['devel_dumper'] = 'var_dumper';
$config['devel.settings']['error_handlers'] = [4 => '4'];
$config['devel.toolbar.settings']['toolbar_items'] = [
  'devel.cache_clear', 'devel.container_info.service', 'devel.field_info_page', 'devel.run_cron', 'devel.admin_settings_link',
  'devel.menu_rebuild', 'devel.reinstall', 'devel.route_info', 'devel.configs_list',
];

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.development.yml';
