<!doctype html>

<head>
    <title>Опрос</title>
    <meta charset="UTF-8">
</head>

<body>
<?php
    ini_set('error_reporting', E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    require_once "classes/Database.php";
    require_once "classes/Poll.php";
    require_once "classes/Question.php";
    require_once "classes/Option.php";
    $poll = Database::getInstance()->getFullPollData(1);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($poll->isValid($_POST["poll"])) {
            Database::getInstance()->saveAnswers($_POST["poll"]);
            header("redirect: polls/statistic?poll_id=" . $poll->getId());
        } else {
            $error = "<div class='error'>Ошибка при заполнение формы. Пожалуйста, проверьте введенные данные.</div>";
        }
    }
    require_once "template.php";
?>
</body>