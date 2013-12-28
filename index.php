<?php

require('vendor/autoload.php');
$config = require('config.php');

$db = new \NeriticArchive\Db($config);

$app = new \NeriticArchive\App($db);
$app->run();
