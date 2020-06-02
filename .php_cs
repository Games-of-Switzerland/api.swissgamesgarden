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

$config = Drupal8::create()
  ->setFinder($finder)
;

$rules = $config->getRules();
$config->setRules($rules);
return $config;
