<?php
require_once 'config.php';
$file = getFile($argv[1]);
$sql = connectDb('wordnet');
$value = wordnet($file);
$sql->query($value);
$sql->close();