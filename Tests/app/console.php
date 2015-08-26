<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

set_time_limit(0);

require_once __DIR__.'/autoload.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;

$kernel = new AppKernel('dev', true);
$application = new Application($kernel);
$application->run();
