<?php
/**
 * Author: Jasmin Stern
 * Date: 25.11.2016
 * Time: 21:33
 */

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

require_once(dirname(__FILE__) . '/../App/config.php');

use App\Model\Program\Program;

/* @var $config array */

$program = new Program($config);
$program->loadJSON();