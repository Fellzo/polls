<?php
    require_once "classes/Database.php";
    $id = $_GET["id"];
    $stat = Database::getInstance()->getQuestionStatistic($id);
    $opt = Database::getInstance()->getQuestionOptions($id);
    echo json_encode(["statistic" => $stat, "options" => $opt]);