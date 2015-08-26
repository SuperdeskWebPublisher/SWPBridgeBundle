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

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/autoload.php';

$kernel = new AppKernel('test', false);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
