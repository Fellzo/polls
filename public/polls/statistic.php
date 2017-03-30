<?php

ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once "classes/Database.php";
require_once "classes/Poll.php";
require_once "classes/Question.php";
require_once "classes/Option.php";

if ($_GET["result"] == "ok") {
    echo "<div class='message'>Спасибо за участие в опросе</div>";
}


$id = intval($_GET['poll_id']);

try {
    $poll = Database::getInstance()->getFullPollData($id)->setCurrMode(POLL::STATISTIC_MODE);
} catch (Error $error) {
    die("Опрос не найден.");
}
require_once "template.php";