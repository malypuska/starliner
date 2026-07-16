<?php

$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
$db['dsn'] = 'mysql:host=MariaDB-10.6;dbname=apple';

return $db;
