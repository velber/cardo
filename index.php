<?php

echo '<meta charset="UTF-8">';

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time', '999');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require 'test/bootstrap.php';

use Makewear\SelectUrls;
use Makewear\Cardo;

$select = new SelectUrls();
$cardo = new Cardo($select);
$allItems = $cardo->getAllItems();
echo 'Всього товарів перевірено - '.count($allItems).'<br>';
$cardo->showDuration();
