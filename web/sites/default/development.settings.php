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

