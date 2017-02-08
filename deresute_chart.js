function draw_chart(received_data)
{
    var chartData = received_data.map(function(x){
	x.date = x.time * 1000;
	return x;
    })

    var chart = AmCharts.makeChart("chartdiv", {
      "period": "YYYY/M/D H:S",
      "type": "serial",
      "theme": "light",
      "language": "ja",
      "legend": {
        "useGraphSettings": true,
        "valueWidth": 50
      },
      "dataProvider": chartData,
      "synchronizeGrid": true,
      "valueAxes": [{
        "id": "v1",
        "axisColor": "#FF6600",
        "axisThickness": 2,
        "axisAlpha": 1,
        "offset": 0,
        "position": "left",
      }, {
        "id": "v2",
        "axisColor": "#FCD202",
        "axisThickness": 2,
        "axisAlpha": 1,
        "offset": 50,
        "position": "left"
      }, {
        "id": "v3",
        "axisColor": "#bfafff",
        "axisThickness": 2,
        "axisAlpha": 1,
        "offset": 100,
        "position": "left"
      }, {
        "id": "v4",
        "axisColor": "#f9bbc7",
        "axisThickness": 2,
        "axisAlpha": 1,
        "offset": 0,
        "position": "right"
      }, {
        "id": "v5",
        "axisColor": "#B0DE09",
        "axisThickness": 2,
        "offset": 150,
        "axisAlpha": 1,
        "position": "left"
      }],
      "graphs": [{
        "valueAxis": "v1",
        "lineColor": "#FF6600",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "レベル",
        "valueField": "level",
        "fillAlphas": 0
      }, {
        "valueAxis": "v2",
        "lineColor": "#FCD202",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "コミュ達成数",
        "valueField": "commu_no",
        "fillAlphas": 0
      }, {
        "valueAxis": "v3",
        "lineColor": "#bfafff",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "アルバム登録数",
        "valueField": "album_no",
        "fillAlphas": 0
      }, {
        "valueAxis": "v4",
        "lineColor": "#f9bbc7",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "ファン",
        "valueField": "fan",
        "fillAlphas": 0
      }, {
        "valueAxis": "v5",
        "lineColor": "#B0DE09",
        "bullet": "round",
        "bulletBorderThickness": 1,
        "lineThickness": 3,
        "hideBulletsCount": 30,
        "title": "PRP",
        "valueField": "prp",
        "fillAlphas": 0
      }],
      "chartScrollbar": {
        "scrollbarHeight": 20,
        "backgroundAlpha": 0,
        "selectedBackgroundAlpha": 0.1,
        "selectedBackgroundColor": "#888888",
        "autoGridCount": true,
        "color": "#AAAAAA"
      },
      "valueScrollbar": {
        "oppositeAxis": false,
        "offset": 10,
        "scrollbarHeight": 10
      },
      "chartCursor": {
        "cursorPosition": "mouse"
      },
      "categoryField": "date",
      "categoryAxis": {
        "axisColor": "#DADADA",
        "minPeriod": "mm",
        "parseDates": true,
        "minorGridEnabled": false
      },
      "export": {
        "enabled": false,
        "position": "bottom-right"
      }
    });

    chart.zoomToIndexes(chart.dataProvider.length - 20, chart.dataProvider.length - 1);
}

function fetch_data_error()
{
}

function fetch_and_draw_daily_chart()
{
    $.ajaxSetup(
	{
	    url: 'fetch_chart_data.php',
	    type: "GET",
	    dataType: "json",
	    timeout: 10000,
	    success: draw_chart,
	    error: fetch_data_error,
	}
    );
    $.ajax();
}
function fetch_and_draw_hourly_chart()
{
    $.ajaxSetup(
	{
	    url: 'fetch_chart_data.php?hourly',
	    type: "GET",
	    dataType: "json",
	    timeout: 10000,
	    success: draw_chart,
	    error: fetch_data_error,
	}
    );
    $.ajax();
}

$(function() {
    fetch_and_draw_daily_chart();

    $('img.lazy').lazyload({
	effect: "fadeIn",
    });

    $("a#daily").on("click", function(e) {
	fetch_and_draw_daily_chart();
    });
    $("a#hourly").on("click", function(e) {
	fetch_and_draw_hourly_chart();
    });
});
