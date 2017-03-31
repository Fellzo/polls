<head>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="vendor/jquery.js"></script>
    <script>
        $(document).ready(function () {
            google.charts.load('current', {'packages': ['corechart', 'bar']});

            $(".question").each(function () {
                var id = this.id;
                console.log("get_question_statistic.php/id=" + id);
                $.ajax({
                    url: "get_question_statistic.php?id=" + id,
                    success: function (res) {
                        console.log(res);
                        res = JSON.parse(res);
                        google.charts.setOnLoadCallback(draw);
                        function draw() {
                            var options = [['Element', 'Проголосовавшие', { role: 'style' }]
                            ];
                            res.options.forEach(
                                function (item) {
                                    console.log(item);
                                    options.push([item.answer, res.statistic[item.value], "blue;"]);
                                }
                            );
                            var data = google.visualization.arrayToDataTable(options);
                            var chart = new google.visualization.BarChart(document.getElementById('chart_' + id));
                            chart.draw(data);
                            $("#" + id).html("");
                        }
                    },
                    error: function (er) {
                        console.log("fail");
                    }
                });
            });

        });

    </script>
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
?>
</body>