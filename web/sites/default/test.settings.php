<?php

/**
 * Extends the development settings and allow override below.
 */
include __DIR__ . '/' . 'development.settings.php';

/**
 * The base URL of sitemap links can be overridden here.
 *
 * @var string
 */
$config['simple_sitemap.settings']['base_url'] = 'https://gos.museebolo.ch';

/**
 * Setting used to add a prefix for ES index based on the environment.
 */
$settings['gos_elasticsearch.index_prefix'] = 'test';
