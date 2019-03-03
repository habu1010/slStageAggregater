<?php
// ツイートする用のファイル getUserDataでrequire_onceするためのファイル 一応単体でも叩ける。

$settings = parse_ini_file("setting.ini");

// データベースからデータを取得
try {
    $pdo = new PDO('sqlite:' . $settings['sqlite3_db_path']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('connection unsuccess' . $e->getMessage());
}

$stmt = $pdo->query('SELECT * FROM slstage_aggregater_daily ORDER BY time DESC LIMIT 2');
$array = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dateTimeImmutable = new DateTimeImmutable();
$today = $dateTimeImmutable->setTimestamp($array[0]['time']);
$yesterday = $dateTimeImmutable->setTimestamp($array[1]['time']);
$first_day_of_month = $yesterday->modify('first day of this month');
$stmt = $pdo->prepare('SELECT * FROM slstage_aggregater_daily WHERE time >= ? ORDER BY time ASC LIMIT 1');
$stmt->execute(array($first_day_of_month->getTimeStamp()));
$array2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$today_date = $today->format('Y/m/d H:i');
$yeasterday_date = $yesterday->format('Y/m/d H:i');
$level = $array[0]['level'];
$level_increment = $array[0]['level'] - $array[1]['level'];
$prp = $array[0]['prp'];
$prp_increment = $array[0]['prp'] - $array[1]['prp'];
$fan = number_format($array[0]['fan']);
$fan_increment = number_format($array[0]['fan'] - $array[1]['fan']);
$fan_increment_month = number_format($array[0]['fan'] - $array2[0]['fan']);

$tweetStr = <<<EOF
#デレステプレイしてますけど
$today_date (vs $yeasterday_date)

レベル： $level (+ $level_increment)
PRP： $prp (+ $prp_increment)
ファン数： $fan (+ $fan_increment)
月間獲得ファン数： $fan_increment_month
詳細： {$settings['detailURL']}
EOF;

require __DIR__ . '/vendor/autoload.php';
use mpyw\Co\Co;
use mpyw\Co\CURLException;
use mpyw\Cowitter\Client;
use mpyw\Cowitter\HttpException;

$twitter = parse_ini_file("twitter.ini");

$client = new Client(
    [$twitter['consumer_key'],
     $twitter['consumer_secret'],
     $twitter['access_token'],
     $twitter['access_token_secret']]
);
//$client = $client->withOptions([CURLOPT_CAINFO => __DIR__ . '/vendor/cacert.pem']);

$client->post('statuses/update', ['status' => $tweetStr]);
