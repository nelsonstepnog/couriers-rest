<?php

require_once __DIR__ . '/../../../Delivery/user-rest/tests/_support/Helper/UserDatabaseHelper.php';
require_once __DIR__ . '/../../geoobjects/tests/_support/Helper/LocationDatabaseHelper.php';

use Codeception\Util\Autoload;

Autoload::addNamespace('Delivery\UserRest\tests\Helper', '../../../../Delivery/user-rest/tests/_support/Helper/');
Autoload::addNamespace('Delivery\Geoobjects\tests\Helper', '../../../geoobjects/tests/_support/Helper/');
