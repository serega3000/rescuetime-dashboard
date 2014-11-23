<?
namespace Zoop\Mirage;
require __DIR__."/vendor/autoload.php";
require 'rescuetime.php';

ini_set('show_errors',1);
error_reporting(E_ALL);

$data = rescuetime_query(array(
	'restrict_kind' => 'productivity',
	'perspective' => 'rank',
	'restrict_end' => date('Y-m-d'),
	'restrict_begin' => date('Y-m-d'),
));


$values = array(
	0 => 0,
	1 => 0,
	2 => 0,
	3 => 0,
	4 => 0,
);
foreach($data->rows as $item_data)
{
	$val = round(floatval($item_data[1]) / 60 / 60, 1);
	$values[$item_data[3] + 2] = $val;
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

$chart = new PieChart('pc', 100, 100);


$data = new ChartData($values);
$data->setColor(array(
	"AA0000",
	"550000",
	"666666",
	"005500",
	"00AA00"
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