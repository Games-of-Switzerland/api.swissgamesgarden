<?php

// phpcs:ignoreFile

$databases['default']['default'] = array (
  'database' => 'drupal_test',
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

/**
 * Setting used to add a prefix for ES index based on the environment.
 */
$settings['gos_elasticsearch.index_prefix'] = 'test';

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
$config['elasticsearch_helper.settings']['hosts'][0]['host'] = 'elasticsearch';
$config['elasticsearch_helper.settings']['hosts'][0]['port'] = '9200';

/**
 * Base URL of the Next App.
 *
 * This value should not contain a leading slash (/).
 */
$config['frontend']['base_url'] = 'https://swissgames.garden';

/**
 * The base URL of sitemap links can be overridden here.
 *
 * @var string
 */
$config['simple_sitemap.settings']['base_url'] = 'https://swissgames.garden';

/**
 * The Symfony Mailer transporter.
 *
 * @var string
 */
$config['symfony_mailer.settings']['default_transport'] = 'smtp';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['host'] = 'mailcatcher';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['port'] = '1025';

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
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('psalm');
