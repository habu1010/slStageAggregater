<?php
/*
deresute.meさんのjsonを拝借してユーザーデータを取得する。
cronでいい感じの感覚で叩くとデータベースに保存される。
*/

$settings = parse_ini_file("setting.ini");


// 最初に時間を求めておく
$GLOBALS['time'] = time();

function printLog($str)
{
    // ログ出力用にまとめたやつ
    $t = sprintf('%.3f', microtime(true) - $GLOBALS['time']);
    echo "(" . $t . "ms) " . $str;
    flush();
    ob_flush();
}

// いつもの
try {
    $pdo = new PDO('sqlite:' . $settings['sqlite3_db_path']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('connection unsuccess' . $e->getMessage());
}
printLog("PDOロード\n");

$pdo->exec(
    <<< EOM
CREATE TABLE IF NOT EXISTS slstage_aggregater(
    time INTEGER PRIMARY KEY,
    level INTEGER,
    commu_no INTEGER,
    album_no INTEGER,
    fan INTEGER,
    prp INTEGER
);
CREATE TABLE IF NOT EXISTS daily(time INTEGER PRIMARY KEY);
CREATE VIEW IF NOT EXISTS slstage_aggregater_daily as SELECT * FROM slstage_aggregater NATURAL INNER JOIN daily;
EOM
);

// jsonを取得する
$url = "https://deresute.me/" . $settings['gameId'] . "/json";
$json = file_get_contents($url);
if ($json === false) {
    exit;
}
$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$arr = json_decode($json, true);
printLog("jsonロード\n");

// 日付が変わって最初のデータかどうか調べる
$stmt = $pdo->query("SELECT date(time, 'unixepoch', 'localtime') FROM daily ORDER BY time DESC LIMIT 1");

$last_dailydata_date = $stmt->fetch(PDO::FETCH_COLUMN);
$today_date = date('Y-m-d', $GLOBALS['time']);

// 送信する配列 あとで書くと長くなるのでここで。
$pushArr = array(
":uptime" => $GLOBALS['time'],
":level" => $arr['level'],
":commu" => $arr['commu_no'],
":album" => $arr['album_no'],
":fan" => $arr['fan'],
":prp" => $arr['prp']
);

// 送信しておしまい 失敗したら次回頑張ろう
if ($pdo->beginTransaction()) {
    try {
        $stmt = $pdo->prepare('INSERT INTO slstage_aggregater (time, level, commu_no, album_no, fan, prp) VALUES (:uptime, :level, :commu,  :album, :fan, :prp)');
        $stmt->execute($pushArr);
        if ($today_date !== $last_dailydata_date) {
            $stmt = $pdo->prepare('INSERT INTO daily (time) VALUES(:uptime)');
            $stmt->bindValue(':uptime', $pushArr[":uptime"]);
            $stmt->execute();
        }
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollback();
        exit('There was an error inserting data into the DB: ' . $e->getMessage());
    }
}

// 1日1回ツイートする処理
if ($today_date !== $last_dailydata_date) {
    echo "日付が変わったので実行";
    include_once('tweet.php');
}
