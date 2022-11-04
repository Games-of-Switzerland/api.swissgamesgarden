CONTRIBUTING
------------

All Analyzers are installed using PHive. Some extra analyzer dependencies are installed using Composer.

## 🐳 Install

1. Setup a PHive authentication file (`.phive/auth.xml`) to prevent Github rate-limit download

```bash
phive skel -a
```

2. Add in this new created file a [Github Token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token)

```xml
<?xml version="1.0" ?>
<auth xmlns="https://phar.io/auth">
    <domain host="api.github.com" type="Token" credentials="GITHUB_TOKEN" />
</auth>
```

3. Install Phars with PHive

```bash
phive install
```

## 🚔 Check Drupal coding standards & Drupal best practices

You need to run composer before using PHPCS. The Drupal and DrupalPractice Standard will automatically be applied following the rules on phpcs.xml.dist` file

### Command Line Usage

Check Drupal coding standards & Drupal best practices:

```bash
./vendor/bin/phpcs
```

Automatically fix coding standards

```bash
./vendor/bin/phpcbf
```

### Improve global code quality using PHPCPD (Code duplication).

Copy/Paste Detector

```bash
./tools/phpcpd ./web/modules/custom \
--names=*.php,*.module,*.inc,*.install,*.test,*.profile,*.theme,*.css,*.info,*.txt --names-exclude=*.md,*.info.yml \
--ansi --exclude=tests

./tools/phpcpd ./behat --names=*.php --ansi
```

### Ensure PHP Community Best Practicies using PHP Coding Standards Fixer

It can modernize your code (like converting the pow function to the ** operator on PHP 5.6) and (micro) optimize it.

We must add one extra dependencies (via Composer) to work properly with Drupal:
- `drupol/phpcsfixer-configs-drupal`

```bash
./vendor/bin/php-cs-fixer fix --dry-run --format=checkstyle
```

### Attempts to dig into your program and find as many type-related bugs as possible via Psalm

We must add one extra dependencies (via Composer) to work properly with Drupal:
- `fenetikm/autoload-drupal`

```bash
./tools/psalm
```

### Catches whole classes of bugs even before you write tests using PHPStan

We must add two extra dependencies (via Composer) to work properly with Drupal:
- `mglaman/phpstan-drupal`
- `phpstan/phpstan-deprecation-rules`

```bash
./tools/phpstan analyse ./web/modules/custom ./behat ./web/themes --error-format=checkstyle
```

### Enforce code standards with git hooks

Maintaining code quality by adding the custom post-commit hook to yours.

```bash
# You can use this in local
cat ./scripts/linters.sh >> ./.git/hooks/post-commit && chmod ugo+x ./.git/hooks/post-commit
#                           OR
# Use this to run into the docker "dev" container.
echo "docker-compose exec dev ./scripts/linters.sh" >> ./.git/hooks/post-commit && chmod ugo+x ./.git/hooks/post-commit
```
