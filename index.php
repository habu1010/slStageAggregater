<?php
$settings = parse_ini_file("setting.ini");
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>デレステプレイしてますけど！</title>
<link rel="stylesheet" href="style.css" />
<link rel="shortcut icon" href="img/favicon.png" type="image/png">

<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
<![endif]-->

<script src="//www.amcharts.com/lib/3/amcharts.js"></script>
<script src="//www.amcharts.com/lib/3/serial.js"></script>
<script src="//www.amcharts.com/lib/3/themes/light.js"></script>
<script src="//www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />

<script
src="https://code.jquery.com/jquery-3.1.1.min.js"
integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
crossorigin="anonymous">
</script>
<script src="jquery.lazyload.min.js"></script>

<script src="deresute_chart.js"></script>

<?php include_once("analytics.php") ?>

</head>
<body>
<header role="banner">
<h1>デレステプレイしてますけど！</h1>
<h2 class="grayMini">User : <?php echo $settings['userName'] . '<a href="https://twitter.com/' . $settings['twitterId'] . '" target="_blank">@' . $settings['twitterId'] . "</a> (" . $settings['gameId'] . ")"?></h2>
</header>
<nav>
<!-- ここにメニューだとか -->
</nav>
<div role="main">
<p>デレステをどれ位やっているか、<a href="https://deresute.me/" target="_blank">deresute.me</a>さんのjsonをお借りしてグラフ化しています。</p>
<p><a href="#" id="daily">簡易表示(１日毎)</a> / <a href="#" id="hourly">詳細表示(１時間毎)</a></p>
<?php echo '<img src="img/spin-black.svg" class="lazy banner" data-original="https://deresute.me/'. $settings['gameId'] .'/medium">' ?>
<div id="chartdiv"></div>
</div>
<footer role="contentinfo">
<p>
©BANDAI NAMCO Entertainment Inc. <br>
©BNEI / PROJECT CINDERELLA
</p>
<p>
<a class="f" href="https://github.com/Slime-hatena/slStageAggregater" target="_blank">slStageAggregater</a> is released under the MIT License by <a class="f" href="https://twitter.com/Slime_hatena" target="_blank">Slime_hatena</a><br>
<a class="f" href="https://github.com/mpyw/cowitter" target="_blank">cowitter</a> under the MIT license by <a class="f" href="https://github.com/mpyw" target="_blank">mpyw</a>
</p>
</footer>
</body>
</html>
