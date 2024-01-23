<?php

header("Content-Type: application/json");

if (!isset($_SESSION)) session_start();

define('ROOT_PATH', realpath(dirname(__FILE__) . "/../") . "/");

require_once ROOT_PATH . "php/DB.php";
$db = DB::getInstance();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['login']))
    echo json_encode(array("error"=>"not logged in"));