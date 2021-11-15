<?php

$autoload = require_once __DIR__ . '/autoload.php';

require_once __DIR__ . '/core/includes/batch.inc';
require_once __DIR__ . '/core/includes/bootstrap.inc';
require_once __DIR__ . '/core/includes/common.inc';
require_once __DIR__ . '/core/includes/errors.inc';
require_once __DIR__ . '/core/includes/file.inc';
require_once __DIR__ . '/core/modules/file/file.module';
require_once __DIR__ . '/core/includes/form.inc';
require_once __DIR__ . '/core/includes/install.core.inc';
require_once __DIR__ . '/core/includes/install.inc';
require_once __DIR__ . '/core/includes/menu.inc';
require_once __DIR__ . '/core/includes/module.inc';
require_once __DIR__ . '/core/includes/schema.inc';
require_once __DIR__ . '/core/includes/theme.inc';
require_once __DIR__ . '/core/includes/theme.maintenance.inc';
require_once __DIR__ . '/core/includes/update.inc';
require_once __DIR__ . '/core/includes/utility.inc';

return $autoload;
