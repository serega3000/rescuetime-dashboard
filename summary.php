<?
namespace Zoop\Mirage;
require dirname(dirname(__FILE__))."/vendor/autoload.php";
require 'resquetime.php';

ini_set('show_errors',1);
error_reporting(E_ALL);


$begin = null;
switch($_GET['time'])
{
	case 'day':		
		$begin = date('Y-m-d');
		break;
	case 'week':		
		$begin = date('Y-m-d', time() - 60*60*24*7);
		break;
	case 'month':		
		$begin = date('Y-m-d', time() - 60*60*24*30);
		//$begin = date('Y-m-01');
		break;
	default:
		throw new Exception('not found time');
}

$data = resquetime_query(array(
	'restrict_kind' => 'productivity',
	'perspective' => 'rank',
	'restrict_end' => date('Y-m-d'),
	'restrict_begin' => $begin,
));


$values = array(
	0 => 0,
	1 => 0,
	2 => 0
);
foreach($data->rows as $item_data)
{
	$productivity = $item_data[3];
	
	if($productivity == -2) $index = 0;
	elseif($productivity == 2) $index = 2;
	else $index = 1;
	
	
	$val = round(floatval($item_data[1]) / 60 / 60, 1);
	$values[$index] += $val;
}


//~ $chart = new PieChart('pc', 500, 200);

//~ $data = new ChartData($values);
//~ $data->setLabelsAuto();
//~ $data->setLegend('Foo');
//~ $chart->addData($data);

//~ $data = new ChartData(array(50,50));
//~ $data->setLabels(array('Foo','Bar'));
//~ $data->setLegend('Foo');
//~ $chart->addData($data);

$chart = new PieChart('pc', 150, 150);


$data = new ChartData($values);
$data->setColor(array(
	"990000",
	"666666",
	"009900"
));
$chart->addData($data);

		
$chart->setQueryMethod(ChartApi::GET);

if ( isset($_GET['debug']) ) {
	var_dump($chart->getQuery());
	echo $chart->validate();
	echo $chart->toHtml();
}
else {
	header('Content-Type: image/png');
	echo $chart;
}