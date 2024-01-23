<?php

require_once "__inc.php";

if (isset($_SESSION['login']))
    include "accueil.inc.php";
else
    include "login.inc.php";