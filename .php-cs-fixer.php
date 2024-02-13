<?php

use drupol\PhpCsFixerConfigsDrupal\Config\Drupal8;

$finder = PhpCsFixer\Finder::create()
  ->in(['web/modules/custom'])
  ->name('*.module')
  ->name('*.inc')
  ->name('*.install')
  ->name('*.test')
  ->name('*.profile')
  ->name('*.theme')
  ->notPath('*.md')
  ->notPath('*.info.yml')
;

$config = new Drupal8();
$config->setFinder($finder);

$rules = $config->getRules();
$rules['global_namespace_import'] = FALSE;
$rules['no_superfluous_phpdoc_tags'] = FALSE;
$rules['ordered_class_elements']['sort_algorithm'] = 'none';

unset($rules['blank_lines_before_namespace']);

$rules['fully_qualified_strict_types'] = [
  'import_symbols' => true,
  'leading_backslash_in_global_namespace' => false,
  'phpdoc_tags' => [],
];

$config->setRules($rules);
return $config;
