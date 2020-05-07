<?php

/**
 * Extends the development settings and allow override below.
 */
include __DIR__ . '/' . 'development.settings.php';

/**
 * Setting used to add a prefix for ES index based on the environment.
 */
$settings['gos_elasticsearch.index_prefix'] = 'test';
