<!DOCTYPE html>
<html lang="en">

<head>
    <title>Statistics</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/all.css" rel="stylesheet"/>
    <link href="css/bootstrap/css/bootstrap.css" rel="stylesheet"/>
    <link href="css/stylebis.css" rel="stylesheet"/>
    <script src="lib/jquery-3.4.1.min.js"></script>
    <script src="lib/chart.js-2.9.3/package/dist/Chart.js"></script>
    <script src="css/bootstrap/js/bootstrap.min.js"></script>
    <script src="view/utilsJs/loadUtils.js"></script>






    <script>
        var stats;
        var days;
        var myChart;
        var period;

        $(function() {
            days = $("#numberOf").val();
            period = $("#period").val();
            $("#numberOf").keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    $("#numberOf").change();

                }
            });

            $("#numberOf").change(function(e) {
                //retrieving days via input  as integer
                days = $("#numberOf").val();
                var val = Math.abs(parseInt(days, 10) || 1);
                days = val > 99 ? 99 : val;
                $(this).val(days);
                getStats();
            });

            $("#period").change(function(e) {
                period = $("#period").val();
                getStats();
            });

            $("#myChart").click(function(e) {
                //when clicking on chart, if it's on one bar, then retrieve user activity and adapt info table with server's answer
                //scrollTop is used to retrieve exact position on the page, without it it could be a bit displeasing
                e.preventDefault();
                var activePoint = myChart.getElementAtEvent(e);
                if (activePoint[0]) {
                    var chartData = activePoint[0]['_chart'].config.data;
                    var idx = activePoint[0]['_index'];
                    var label = chartData.labels[idx];
                    var value = chartData.datasets[0].data[idx];
                    $("#legend").empty();
                    $("#legend").append("Detailed activity for " + label);
                    let pos =$(document).scrollTop();
                    $.get("user/get_recent_activity/" + label + "/" + days + "/" + period,
                        function(data) {
                            fillInfoTable(data);
                            $(document).scrollTop(pos);
                        });
                }

            });
            getStats();
        });

        function fillInfoTable(infos) {
            infos = JSON.parse(infos);
            infos.sort(function(a, b) {
                return new Date(b.time) - new Date(a.time);
            });

            $("#infoBody").empty();
            var body = ""
            for (var i in infos) {
                body += "<tr><td>" + infos[i].time + "</td><td>" + infos[i].type + "</td><td>" + infos[i].content + "</td></tr>";
            }
            if (!$("#infoTable").is(":visible"))
                $("#infoTable").toggle(400);
            $("#infoBody").append(body);
        }

        function sortByActivity(a, b) {
            return b.activity - a.activity;
        }

        function sortStats() {
            stats.sort(sortByActivity);
        }

        function getStats() {
            $("#infoTable").hide();
            $.get("user/get_activity_as_json/" + days + "/" + period,
                function(data) {
                    stats = JSON.parse(data);
                    sortStats();
                    showGraph();
                });
        }

        function showGraph() {
            var name = [];
            var activity = [];
            for (var i in stats) {
                name.push(stats[i].name);
                activity.push(stats[i].activity);
            }

            var chartdata = {
                labels: name,
                datasets: [{
                    label: 'Activity',
                    backgroundColor: 'rgba(30,144,255,.4)',
                    borderColor: '#46d5f1',
                    hoverBackgroundColor: 'rgba(30,144,255,.6)',
                    hoverBorderColor: '#666666',
                    data: activity
                }]

            };
            var graphTarget = $("#myChart");
            if (myChart) {
                myChart.destroy();
            }
            myChart = new Chart(graphTarget, {
                type: 'bar',
                data: chartdata,
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Most active users',
                        fontSize: 14,
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    }
                },
            });
            myChart.render();
        }
    </script>

</head>
<body class="bg p-5">

    <?php require("view/menu.php"); ?>
        <div class="container-xl bg-light mt-5 lessWidth pt-2 rounded">
            <form class="d-flex justify-content-center align-items-center mb-5 mt-2 mt-4">
                <h5>Period, Last :</h5>
                <input type="number" id="numberOf" class="mx-2 form-control w-auto" value="1" name="numberOf" min="1" max="99" />
                <select class="form-control w-auto mx-2" id="period">
                    <option>Days</option>
                    <option>Year</option>
                    <option>Month</option>
                    <option>Week</option>
                </select>
            </form>
            <canvas class="lessWidth" id="myChart"></canvas>
            <div class="px-5 mx-5" id="infoTable">
                <h4 id="legend" class="mt-5 pt-0 border-bottom pb-2 text-muted border-muted">tt
                    </h4>
                    <table class="table mt-3 text-center">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Timestamp</th>
                                <th scope="col">Action</th>
                                <th scope="col">Title</th>
                            </tr>
                        </thead>
                        <tbody id="infoBody">
                        </tbody>
                    </table>
            </div>
        </div>
        <?php require_once("view/loginModal.php"); ?>
        <?php require_once("view/logoutModal.php"); ?>
        <?php require_once("view/signupModal.php"); ?>


    </body>

</html>