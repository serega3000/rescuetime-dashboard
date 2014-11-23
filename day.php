<?
namespace Zoop\Mirage;
require __DIR__."/vendor/autoload.php";
require 'rescuetime.php';

ini_set('show_errors',1);
error_reporting(E_ALL);

$data = rescuetime_query(array(
	'restrict_kind' => 'productivity',
	'perspective' => 'interval',
	'resolution_time' => 'hour',
	'restrict_end' => date('Y-m-d'),
	'restrict_begin' => date('Y-m-d'),
));


$values = array(
	-2 => array(),
	-1 => array(),
	0 => array(),
	1 => array(),
	2 => array(),
	3 => array()
);

$labels = array();

for($i = 0; $i < 24; $i++)
{
	for($j = -2; $j <=3; $j++)
	{
		$values[$j][$i] = 0;
	}
	if(($i + 1) % 2 == 0)
	{
		$labels[$i] = $i;
	}
	
	
	$plan = 0;
	
	if($i >=9 && $i <=12)
	{
		$plan = 5 / 6;
	}
	elseif($i >=14 && $i <=17)
	{
		$plan = 5 / 6;
	}
	$values[3][$i] = $plan;	
}



$max = 0;

foreach($data->rows as $item_data)
{
	$val = floatval($item_data[1]) / 3600;
	$max = max($val, $max);
	$hour = intval(date("H",  strtotime($item_data[0])));
	
	$values[$item_data[3]][$hour] = $val;	
}
//var_dump($values);
//die();

//$max = floatval(ceil($max * 2)) / 2;

$chart = new Chart('lc', 340, 150);
$chart->setGridLines(100 / 22 * 2,100 / $max / 2);
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



/**
 * Very distracting
 */


$line = new ChartData($values[-2]);
//$line->setLegend('VD');
$line->setColor('AA0000');
$chart->addData($line);
$line->setThickness(3);
/*
$marker = new Markers\ChartShapeMarker(Markers\ChartShapeMarker::CIRCLE);
$marker->setData($line);
$marker->setSize(5);
$marker->setColor('FF0000');
$chart->addMarker($marker);
*/




/**
 * Very productive
 */




$line = new ChartData($values[2]);
//$line->setLegend('VP');
$line->setColor('00AA00');
$line->setThickness(3);

$chart->addData($line);
/*
$marker = new Markers\ChartShapeMarker(Markers\ChartShapeMarker::CIRCLE);
$marker->setData($line);
$marker->setSize(5);
$marker->setColor('00CC00');
$chart->addMarker($marker);

*/

















$y_axis = new ChartAxis('y');
$y_axis->setRange(0, $max);
$chart->addAxis($y_axis);

$x_axis = new ChartAxis('x');
$x_axis->setLabels($labels);
$x_axis->setLabelAlignment($alignment);
//$x_axis->setRange(0,24,2);
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
