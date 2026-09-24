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
