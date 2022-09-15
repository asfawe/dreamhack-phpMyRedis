<?php
    include_once "./core.php";
?>
<html>
    <head></head>
    <link rel="stylesheet" href="/static/bulma.min.css" />
    <body>
        <div class="container card">
            <div class="card-content">
                <div class="columns">
                    <div class="column is-10">
                        <h1 class="title">phpMyRedis</h1>
                    </div>
                    <div>
                        <div class="column is-2"><a href="/config.php" class="card-footer-item">Config</a></div>
                    </div>
                </div>
                <form method="post">
                    <div class="field">
                        <label class="label">Command</label>
                        <div class="control">
                            <textarea class="textarea" name="cmd"><?=isset($_POST['cmd'])?$_POST['cmd']:'return 1;'?></textarea>
                        </div>
                        <label class="checkbox">
                            <input type="checkbox" name="save">Save
                        </label>
                    </div>
                    <div class="control">
                        <input class="button is-success" type="submit" value="submit">
                    </div>
                </form>
                <?php 
                    if(isset($_POST['cmd'])){
                        $redis = new Redis();
                        $redis->connect($REDIS_HOST);
                        $ret = json_encode($redis->eval($_POST['cmd'])); // eval은 함수는 명령어를 실행해주는 함수이다. 하지만 특정 명령어가 있다.
                        echo '<h1 class="subtitle">Result</h1>';
                        echo "<pre>$ret</pre>";
                        if (!array_key_exists('history_cnt', $_SESSION)) { // 입력 확인 부분 입니다.
                            $_SESSION['history_cnt'] = 0;
                        }
                        $_SESSION['history_'.$_SESSION['history_cnt']] = $_POST['cmd'];
                        $_SESSION['history_cnt'] += 1;

                        if(isset($_POST['save'])){
                            $path = './data/'. md5(session_id());
                            $data = '> ' . $_POST['cmd'] . PHP_EOL . str_repeat('-',50) . PHP_EOL . $ret;
                            file_put_contents($path, $data);
                            echo "saved at : <a target='_blank' href='$path'>$path</a>";
                        }
                    }
                ?>
            </div>
        </div>
        <br/>
        <div class="container card">
            <div class="card-content">
                <div class="columns">
                    <div class="column is-10">
                        <h1 class="title">Command History</h1>
                    </div>
                    <div class="column is-2"><a href="/reset.php" class="card-footer-item">Reset</a></div>
                </div>
                <div class="content">
                    <ul>
                    <?php
                        for($i=0; $i<$_SESSION['history_cnt']; $i++){
                            echo "<li>".$_SESSION['history_'.$i]."</li>";
                        }
                    ?>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>