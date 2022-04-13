<?php
declare(strict_types=1);

require_once 'MyPDO.template.php';

MyPDO::setConfiguration('mysql:host=localhost;dbname=test;charset=utf8', 'wesports', 'mysuperpassword');
