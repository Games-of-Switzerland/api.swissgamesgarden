<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\AnnotationToAttributeRector;
use DrupalRector\Drupal10\Rector\ValueObject\AnnotationToAttributeConfiguration;
use DrupalRector\Set\Drupal10SetList;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
  // Adjust the set lists to be more granular to your Drupal requirements.
  // @todo find out how to only load the relevant rector rules.
  //   Should we try and load \Drupal::VERSION and check?
  //   new possible option with ComposerTriggeredSet
  //   https://github.com/rectorphp/rector-src/blob/b5a5739b7d7dde621053adff113449860ed5331f/src/Set/ValueObject/ComposerTriggeredSet.php
  $rectorConfig->sets([
    // Drupal.
    Drupal10SetList::DRUPAL_103,

    // PHP.
    LevelSetList::UP_TO_PHP_83,

    // PHPUnit migration sets - upgrade to PHPUnit 11.
    PHPUnitSetList::PHPUNIT_100,
    PHPUnitSetList::PHPUNIT_110,
  ]);

  // Annotation to Attributes.
  $rectorConfig->ruleWithConfiguration(AnnotationToAttributeRector::class, [
    // Plugins converted from Annotations to Attributes in 10.2.0
    // @see https://www.drupal.org/node/3395575
    new AnnotationToAttributeConfiguration('10.2.0', '10.2.0', 'Block', 'Drupal\Core\Block\Attribute\Block'),
    // Plugins converted from Annotations to Attributes in 10.3.0
    // @see https://www.drupal.org/node/3229001
    new AnnotationToAttributeConfiguration('10.3.0', '10.3.0', 'QueueWorker', 'Drupal\Core\Queue\Attribute\QueueWorker'),
    new AnnotationToAttributeConfiguration('10.3.0', '10.3.0', 'FieldType', 'Drupal\Core\Field\Attribute\FieldType'),
    new AnnotationToAttributeConfiguration('10.3.0', '10.3.0', 'FieldWidget', 'Drupal\Core\Field\Attribute\FieldWidget'),
    new AnnotationToAttributeConfiguration('10.3.0', '10.3.0', 'FieldFormatter', 'Drupal\Core\Field\Attribute\FieldFormatter'),
    // Entity type plugins converted from Annotations to Attributes in 11.1.0
    // @see https://www.drupal.org/node/3505422
    new AnnotationToAttributeConfiguration('11.1.0', '11.1.0', 'ContentEntityType', 'Drupal\Core\Entity\Attribute\ContentEntityType'),
  ]);

  if (class_exists('DrupalFinder\DrupalFinderComposerRuntime')) {
    $drupalFinder = new DrupalFinder\DrupalFinderComposerRuntime();
  } else {
    $drupalFinder = new DrupalFinder\DrupalFinder();
    $drupalFinder->locateRoot(__DIR__);
  }
  $drupalRoot = $drupalFinder->getDrupalRoot();
  $rectorConfig->autoloadPaths([
    $drupalRoot . '/core',
    $drupalRoot . '/modules',
    $drupalRoot . '/profiles',
    $drupalRoot . '/themes'
  ]);

  $rectorConfig->paths([
    __DIR__.'/web/modules/custom',
  ]);
  $rectorConfig->skip([
    '*/upgrade_status/tests/modules/*',
    // Misresolves parent:: calls that cross the custom -> contrib module
    // boundary (e.g. ElasticsearchIndex plugins), silently replacing them
    // with NULL instead of leaving the call in place.
    RemoveParentCallWithoutParentRector::class,
  ]);
  $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);
  $rectorConfig->importNames(true, false);
  $rectorConfig->importShortClasses(false);
};
