<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Опросник</title>
    <style>
        body {
            text-align: center;
        }

        #content {
            display: inline-block;
            width: 50%;
        }

        .question li {
            text-align: left;
        }

        .question {
            list-style: none;
        }

        .question_text {
            font-size: 14pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="content">
       <?php
       /** @var Poll $poll */
       echo $poll->render();
       ?>
    </div>
</body>
</html>