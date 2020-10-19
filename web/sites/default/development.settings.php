<?php

/**
 * Setting used to add a prefix for ES index based on the environment.
 */
$settings['gos_elasticsearch.index_prefix'] = 'development';

/**
 * The elasticsearch host port number.
 *
 * @var string
 */
$config['elasticsearch_helper.settings']['elasticsearch_helper']['port'] = '9200';

/**
 * The elasticsearch host name.
 *
 * @var string
 */
$config['elasticsearch_helper.settings']['elasticsearch_helper']['host'] = 'elasticsearch';

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
\Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('psalm');
