CONTRIBUTING
------------

All Analyzers are installed as Standalone via Composer in their own directory under `tools` tree.

## Check Drupal coding standards & Drupal best practices.

The Drupal and DrupalPractice Standard will automatically be applied following the rules on `phpcs.xml.dist` file.

```bash
./vendor/bin/phpcs
```

Automatically fix coding standards

```bash
./vendor/bin/phpcbf
```

## Analyzer of PHP code to search usages of deprecated functionality using PhpDeprecationDetector.

Analyzer of PHP code to search usages of deprecated functionality in newer interpreter versions

```bash
./tools/php-deprecation-detector/vendor/bin/phpdd --target 8.1 \
--file-extensions php,module,inc,install,test,profile,theme,info \
./web/modules/custom

./tools/php-deprecation-detector/vendor/bin/phpdd --target 8.1 --file-extensions php ./behat
```

## Ensure PHP Community Best Practices using PHP Coding Standards Fixer

It can modernize your code (like converting the pow function to the ** operator on PHP 5.6) and (micro) optimize it.

```bash
./tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --format=checkstyle
```

### Attempts to dig into your program and find as many type-related bugs as possible via Psalm

```bash
./tools/psalm/vendor/bin/psalm
```

### Catches whole classes of bugs even before you write tests using PHPStan

```bash
./vendor/bin/phpstan analyse ./web/modules/custom ./behat ./web/themes --error-format=checkstyle --memory-limit=1024M
```
