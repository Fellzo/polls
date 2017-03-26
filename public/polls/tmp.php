<?php

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);


    require_once "classes/Poll.php";
    require_once "classes/Question.php";
    require_once "classes/Option.php";

    $options = [new Option('kek?', 23), new Option('dek?', 1)];
    $question = new Question('Yoba eto ti?', 0, $options);
    $poll = new Poll('Allah', 'Bugurt', [$question]);
    $poll->saveAll();
    echo "All are saved";