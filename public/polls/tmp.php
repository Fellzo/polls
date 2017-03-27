<?php

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);


    require_once "classes/Poll.php";
    require_once "classes/Question.php";
    require_once "classes/Option.php";
    require_once "classes/Database.php";

    $poll = Database::getInstance()->getFullPollData(17);
    echo $poll->render();