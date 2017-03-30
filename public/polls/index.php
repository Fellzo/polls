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
    if (isset($_COOKIE["poll-{$poll->getId()}"])) {
        header("Location: /polls/statistic.php?poll_id=" . $poll->getId());
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($poll->isValid($_POST["poll"])) {
            Database::getInstance()->saveAnswers($_POST["poll"]);
            // Lifetime of cookie 2 month
            setcookie("poll-{$poll->getId()}", "finished", time() + 60 * 60 * 24 * 60);
            header("Location: /polls/statistic.php?result=ok&poll_id=" . $poll->getId());
        } else {
            $error = "<div class='error'>Ошибка при заполнение формы. Пожалуйста, проверьте введенные данные.</div>";
        }
    }
    require_once "template.php";
?>
</body>