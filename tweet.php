<?php
// ツイートする用のファイル getUserDataでrequire_onceするためのファイル 一応単体でも叩ける。

$settings = parse_ini_file("setting.ini");

require __DIR__ . '/vendor/autoload.php';
use mpyw\Co\Co;
use mpyw\Co\CURLException;
use mpyw\Cowitter\Client;
use mpyw\Cowitter\HttpException;

// データベースからデータを取得
try {
    $pdo = new PDO ( 'sqlite:' . $settings['sqlite3_db_path']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch ( PDOException $e ) {
    exit ( 'connection unsuccess' . $e->getMessage () );
}


$aaa = "%" . date("Y/m/d",strtotime("-1 day")) . "%00%00%";
$stmt = $pdo->prepare('SELECT * FROM slstage_aggregater WHERE time_str LIKE :likes ORDER BY time ASC');
$stmt->bindParam(":likes" , $aaa);
$stmt->execute();
$array[0] = $stmt->fetch();

$aaa = "%" . date("Y/m/d",strtotime("-2 day")) . "%00%00%";
$stmt = $pdo->prepare('SELECT * FROM slstage_aggregater WHERE time_str LIKE :likes ORDER BY time ASC');
$stmt->bindParam(":likes" , $aaa);
$stmt->execute();
$array[1] = $stmt->fetch();



$level = $array[0]['level'];
$level_increment = $array[0]['level'] - $array[1]['level'];
$fan = number_format($array[0]['fan']);
$fan_increment = number_format($array[0]['fan'] - $array[1]['fan']);

$tweetStr = <<< EOF
#デレステプレイしてますけど
{$array[0]['time_str']} (vs {$array[1]['time_str']} )

レベル： $level (+ $level_increment)
ファン数： $fan (+ $fan_increment)
詳細： {$settings['detailURL']}
EOF;

echo "<pre>" . $tweetStr . "</pre>";

$twitter = parse_ini_file("twitter.ini");

$client = new Client(
    [$twitter['consumer_key'],
     $twitter['consumer_secret'],
     $twitter['access_token'],
     $twitter['access_token_secret']]);
//$client = $client->withOptions([CURLOPT_CAINFO => __DIR__ . '/vendor/cacert.pem']);

$client->post('statuses/update', ['status' => $tweetStr]);
