<?php
/*
deresute.meさんのjsonを拝借してユーザーデータを取得する。
cronでいい感じの感覚で叩くとデータベースに保存される。
*/

$settings = parse_ini_file("setting.ini");


// 最初に時間を求めておく 
$GLOBALS['time'] = time();
$GLOBALS['time_str'] = date("Y/m/d H:i",$GLOBALS['time']);

function printLog($str){
    // ログ出力用にまとめたやつ
    $t = sprintf('%.3f', microtime(true) - $GLOBALS['time']);
    echo "(" . $t . "ms) " . $str;
    flush();
    ob_flush();
}

// いつもの
try {
    $pdo = new PDO ( 'sqlite:' . $settings['sqlite3_db_path']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch ( PDOException $e ) {
    exit ( 'connection unsuccess' . $e->getMessage () );
}
printLog("PDOロード\n");

$pdo->exec( <<< EOM
CREATE TABLE IF NOT EXISTS slstage_aggregater(
    time INTEGER PRIMARY KEY,
    time_str TEXT,
    level INTEGER,
    commu_no INTEGER,
    album_no INTEGER,
    fan INTEGER,
    prp INTEGER
)
EOM
);

// jsonを取得する
$url = "https://deresute.me/" . $settings['gameId'] . "/json";
$json = file_get_contents($url);
if ($json === FALSE) {
    exit;
}
$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$arr = json_decode($json, true);
printLog("jsonロード\n");

// 送信する配列 あとで書くと長くなるのでここで。
$pushArr = array(
":uptime" => $GLOBALS['time'],
":time_str" => $GLOBALS['time_str'],
":level" => $arr['level'],
":commu" => $arr['commu_no'],
":album" => $arr['album_no'],
":fan" => $arr['fan'],
":prp" => $arr['prp']
);

// 送信しておしまい 失敗したら次回頑張ろう
$sql = 'INSERT INTO slstage_aggregater (time ,time_str ,level ,commu_no ,album_no ,fan ,prp) VALUES (:uptime , :time_str , :level , :commu , :album , :fan , :prp)';
$stmt=$pdo->prepare($sql);
$res=$stmt->execute($pushArr);
if ($res) {
   printLog("insert成功\n");
}else{
   printLog("insert失敗\n");
}

// 指定時間にツイートする処理
if (date("H") == 0){
    echo "０時なので実行";
    include_once('tweet.php');
}