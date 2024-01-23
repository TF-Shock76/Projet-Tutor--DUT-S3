<?php

if (!isset($_SESSION)) session_start();

define('ROOT_PATH', realpath(dirname(__FILE__)."/../")."/");

require_once ROOT_PATH . "twig/lib/Twig/Autoloader.php";
Twig_Autoloader::register();
$twig = new Twig_Environment(new Twig_Loader_Filesystem([ROOT_PATH."tpl", "./tpl"]));

require_once ROOT_PATH."php/DB.php";
$db = DB::getInstance();

require_once ROOT_PATH."php/func.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['login']) ||
    strpos($db->getUtilisateur($_SESSION['login'])->getRoles(), "A") === false)
    header("Location: /index.php");