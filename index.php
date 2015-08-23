<?php
require 'test/bootstrap.php';

use Makewear\SelectUrls;
use Makewear\Cardo;

$select = new SelectUrls();
$cardo = new Cardo($select);
$allItems = $cardo->getAllItems();
$cardo->showDuration();