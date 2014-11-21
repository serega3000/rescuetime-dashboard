<?
namespace Zoop\Mirage;
require dirname(dirname(__FILE__))."/vendor/autoload.php";
require 'resquetime.php';

ini_set('show_errors',1);
error_reporting(E_ALL);

$end = strtotime('today');
$begin = strtotime('-1 month', $end);

$values = array();
$labels = array();

for($i = -2; $i <= 2; $i ++)
{
	$values[$i] = array();
	
	for($current_day = $begin; $current_day <= $end; $current_day = strtotime('tomorrow', $current_day))
	{
		$index = date('d.m', $current_day);
		$values[$i][$index] = 0;		
		if(!isset($labels[$index]))
		{
			$labels[$index] = date('d', $current_day);
			
			$day_of_week = intval(date('w', $current_day));
			$values[3][$index] = in_array($day_of_week, array(0,6)) ? 0 : 4;				
		}
	}
}

$total_points = count($values[0]);


$data = resquetime_query(array(
	'restrict_kind' => 'productivity',
	'perspective' => 'interval',
	'resolution_time' => 'day',
	'restrict_end' => date('Y-m-d', $end),
	'restrict_begin' => date('Y-m-d', $begin),
));



$max = 0;

foreach($data->rows as $item_data)
{
	$val = floatval($item_data[1]) / 3600;
	$time = strtotime($item_data[0]);
	$max = max($val, $max);
	$values[$item_data[3]][date('d.m', $time)] = $val;
}

$chart = new Chart('lc', 800, 150);
$chart->setGridLines(100/($total_points - 1),100/$max * 2, 2, 5);
//$chart->setLegendPosition('r');
//~ $chart->setMargin(50);
//$chart->setLegendSize(30, 20);
$chart->setFill('ffffff');
//$chart->setGradientFill(45, array('cccccc', 'ffffff', 'cccccc'), Chart::CHART_AREA);





/**
 * distracting
 */


$line = new ChartData($values[-1]);
//$line->setLegend('VD');
$line->setColor('990000');
$chart->addData($line);
$line->setThickness(1);
/*
$marker = new Markers\ChartShapeMarker(Markers\ChartShapeMarker::CIRCLE);
$marker->setData($line);
$marker->setSize(5);
$marker->setColor('FF0000');
$chart->addMarker($marker);
*/

/**
 * productive
 */


$line = new ChartData($values[1]);
//$line->setLegend('VD');
$line->setColor('009900');
$chart->addData($line);
$line->setThickness(1);
/*
$marker = new Markers\ChartShapeMarker(Markers\ChartShapeMarker::CIRCLE);
$marker->setData($line);
$marker->setSize(5);
$marker->setColor('FF0000');
$chart->addMarker($marker);
*/



/**
 * neutral
 */


$line = new ChartData($values[0]);
//$line->setLegend('VD');
$line->setColor('999999');
$chart->addData($line);
$line->setThickness(1);
/*
$marker = new Markers\ChartShapeMarker(Markers\ChartShapeMarker::CIRCLE);
$marker->setData($line);
$marker->setSize(5);
$marker->setColor('FF0000');
$chart->addMarker($marker);
*/



/**
 * plan
 */


$line = new ChartData($values[3]);
//$line->setLegend('VD');
$line->setColor('00AA00');
$line->setDash(2, 4);
$chart->addData($line);
$line->setThickness(2);
/*
$marker = new Markers\ChartShapeMarker(Markers\ChartShapeMarker::CIRCLE);
$marker->setData($line);
$marker->setSize(5);
$marker->setColor('FF0000');
$chart->addMarker($marker);
*/



/**
 * Very distracting
 */


$line = new ChartData($values[-2]);
//$line->setLegend('VD');
$line->setColor('AA0000');
$chart->addData($line);
$line->setThickness(3);

$marker = new Markers\ChartShapeMarker(Markers\ChartShapeMarker::CIRCLE);
$marker->setData($line);
$marker->setSize(7);
$marker->setColor('AA0000');
$chart->addMarker($marker);





/**
 * Very productive
 */




$line = new ChartData($values[2]);
//$line->setLegend('VP');
$line->setColor('00AA00');
$line->setThickness(3);

$chart->addData($line);

$marker = new Markers\ChartShapeMarker(Markers\ChartShapeMarker::CIRCLE);
$marker->setData($line);
$marker->setSize(7);
$marker->setColor('00AA00');
$chart->addMarker($marker);



















$y_axis = new ChartAxis('y');
$y_axis->setRange(0, $max);
$chart->addAxis($y_axis);

$x_axis = new ChartAxis('x');
$x_axis->setLabels($labels);
$x_axis->setLabelAlignment($alignment);
$x_axis->setTickMarks(5);
$x_axis->setDrawLine(false);
$x_axis->setTickColor('00CC00');
$chart->addAxis($x_axis);

if ( isset($_GET['debug']) ) {
	var_dump($chart->getQuery());
	echo $chart->validate();
	echo $chart->toHtml();
}
else{
	header('Content-Type: image/png');
	echo $chart;
}
