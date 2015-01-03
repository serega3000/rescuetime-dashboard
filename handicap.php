<?php

require_once __DIR__."/init.php";

$first_day_of_year = strtotime("01.01.".date("Y"));

$data = rescuetime_query(array(
	'restrict_kind' => 'productivity',
	'perspective' => 'rank',
	'restrict_end' => date('Y-m-d'),
	'restrict_begin' => date('Y-m-d', $first_day_of_year),
));

$current_time = 0;

foreach($data->rows as $item_data)
{
	if($item_data[3] == 2)
	{
		$current_time += $item_data[1];
	}
}


$current_day_of_year = intval(date('z'));

$total_need = 1300 * 60 * 60;

$current_part_of_year = ($current_day_of_year + intval(date("G")) / 24 + intval(date('i')) / 60 / 24) / 365;
$current_need = $total_need * $current_part_of_year;
$handicap = $current_time - $current_need;

echo "<pre>";
echo "$first_day_of_year\n".date('d.m.Y',$first_day_of_year)."\n";
echo "current_percent $current_part_of_year\n";
echo "total_need $total_need\n";
echo "current_need $current_need\n";
echo "handicap ".  format_time($handicap)."\n";
echo "current_time $current_time\n";


