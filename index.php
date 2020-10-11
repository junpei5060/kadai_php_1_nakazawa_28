<?php
$fileName = "test.dat";
$errMessage = "エラーが発生しました。";

$answer = $_POST['hidden-btn'];

clearstatcache();


$isExist = file_exists($fileName);

if($isExist){

    $array = unserialize(file_get_contents($fileName));
    //var_dump($array);
    if(!$array){
        $data = [0,0,0];
    } else {
        $data = $array;
    }

    if($answer == "1"){
        
        $ansResult = $data[0];
        $ansResult++;
        $data[0] = $ansResult;
    } else if ($answer == "2") {
        $ansResult = $data[1];
        $ansResult++;
        $data[1] = $ansResult;
    } else if ($answer == "3") {
        $ansResult = $data[2];
        $ansResult++;
        $data[2] = $ansResult;
    } else {
    }

    
    file_put_contents($fileName, serialize($data), LOCK_EX);

} else {
    // 存在しない場合は新しく作成
    $result = touch($fileName);
    if(!$result){
        // 作成失敗
        echo $errMessage.' : ファイルの新規作成に失敗しました。';
        exit();
    }

    $data = [0,0,0];



    if($answer == "1"){
        $ansResult = $data[0];
        $ansResult++;
        $data[0] = $ansResult;
    } else if ($answer == "2") {
        $ansResult = $data[1];
        $ansResult++;
        $data[1] = $ansResult;
    } else if ($answer == "3") {
        $ansResult = $data[2];
        $ansResult++;
        $data[2] = $ansResult;
    } else {
    }
    file_put_contents($fileName, serialize($data), LOCK_EX);
}

clearstatcache();

if($_POST['submit']){
    ### POSTされたときの処理、省略 ###
    $uri = $_SERVER['HTTP_REFERER'];
    header("Location: ".$uri);
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>アンケート</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.0/Chart.min.js"></script>
    </head>
    <body>
        <h1>好きな賭け方は？</h1>
        <p>1,単勝・複勝
        </p>
        <p>2,馬単・馬連</p>
        <p>3,三連単・三連複</p>
            <button class="question-btn">1</button>
            <button class="question-btn">2</button>
            <button class="question-btn">3</button>
        <form id="question-form" action="index.php" method="post">
            <input type="hidden" id="hidden-btn" name="hidden-btn" value="">
        </form>
        <div id="chart">
            <canvas id="myChart" width="200" height="200"></canvas>
            <p id="noChart">まだ投票がありません。</p>
        </div>
    </body>
    <script>
        $(function(){
            $('.question-btn').click(function(){
                var form1 = document.forms['question-form'];
                //
                // バリデーションチェックや、データの加工を行う。
                //
                var ans = $(this).text();
                $('#hidden-btn').val(ans);

                form1.submit();
                return false;
            });

            drawChart();
        })

        var drawChart = function(){
            var array = <?php echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            console.log(array);
            var matchNum = 0;
            for(var i = 0; i < array.length; i++){
                if(array[i] === 0){
                    matchNum++;
                }
            }
            if(array.length === matchNum ){
                $('#myChart').hide();
                $('#noChart').show();
            } else {
                $('#myChart').show();
                $('#noChart').hide();
            }

            var ctx = document.getElementById("myChart").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ["1:単勝・複勝", "2:馬単・三連単", "3:三連単・三連複"],
                    datasets: [{
                        label: 'アンケート',
                        data: array,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                        ],

                    }]
                }
            });
        }
    </script>
    <style>
        #chart {
            width: 400px;
            height: 400px;
        }
    </style>
</html>