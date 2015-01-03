<?php
require 'rescuetime.php';

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

$total_need = get_config('year_target') * 60 * 60;

$current_part_of_year = ($current_day_of_year + intval(date("G")) / 24 + intval(date('i')) / 60 / 24) / 365;
$current_need = $total_need * $current_part_of_year;
$handicap = $current_time - $current_need;

$handicap_class = "red";
if($handicap > 0)
{
	if($handicap > 60 * 60 * 10)
	{
		$handicap_class = "green";
	}
	else 
	{
		$handicap_class = "yellow";
	}
}



function get_cels($kelv)
{
	
	return round($kelv - 273.15, 1);
}

$weather_data = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?q='.get_config('city').'&lang='.get_config('lang')));

?>
<html>
	
	<head>		
    <meta http-equiv="refresh" content="300">
    <meta charset="utf-8" />
    
	<style>
		*{
			margin:0;
			padding:0;
		}
		table.top{
			font-size: 45px;
			width: 800px;
		}
		table.top td{
			white-space: nowrap;
		}
		table.top td{
			padding: 0 10px;
		}
		table.top td.c{
			text-align: center;			
		}
		table.top td.r{
			text-align: right;
		}
		div.clock{
			height: 80px;
			text-align:left;
			vertical-align: middle;
			font-size: 50px;
			width: 800px;
			line-height: 80px;
			padding: 0 10px;
		}
		div.clock .time_sum{
			float: right;
		}
		table.top td div{
			font-size: 20px;
			display: inline-block;
			vertical-align: top;
			height: 20px;
			margin-right: 10px;
			line-height: 20px;
			text-align:left;
		}
		div.weather{
			display:inline-block;
			height: 100px;
			width: 180px;
			text-align:center;
			vertical-align: middle;
			font-size: 50px;
			line-height: 100px;
		}
		div.weather big{
			
		}
		img{
			vertical-align:middle;
		}
		#body{
			padding: 5px 0;
		}
		table.summary{
			margin: 5px 0;
			border: 0;
			border-collapse: collapse;
		}
		table.summary th, table.summary td{
			border: 0;
		}
		table.top img{
			vertical-align: top;
			margin-top: -5px;
		}
        div.bar{
            width: 100%;
            margin: 0 0 10px;
            height: 20px;
            outline: 1px solid #990000;            
        }
        div.bar div{
            float: left;
            height: 20px;
            background: #009900;
        }
		.handicap{
			padding: 3px;
			height: 1em!important;
			line-height: 1em!important;
			font-size: 0.7em!important;
			font-weight:bold;
		}
		.handicap_yellow {
			background: yellow;
		}
		.handicap_green {
			background: #77ff77;
		}
		.handicap_red {
			background: #ff7777;			
		}
		</style>		
		<script>		


			function onload()
			{
				function checkTime(i)
				{
					if (i<10)
					  {
						i="0" + i;
					  }
					return i;
				}		

				var currentTime = {
					h: <?=date('H')?>,
					m: <?=date('i')?>,
					s: <?=date('s')?>

				}

				function startTime()
				{
					currentTime.s++;
					if(currentTime.s >= 60)
					{
						currentTime.s = 0;
						currentTime.m++;
						if(currentTime.m >= 60)
						{
							currentTime.m = 0;
							currentTime.h++;
							if(currentTime.h >= 24)
							{
								currentTime.h = 0;
							}
						}
					}	

					var h=currentTime.h;
					var m=currentTime.m;
					var s=currentTime.s;
					// add a zero in front of numbers<10
					h=checkTime(h);
					m=checkTime(m);
					s=checkTime(s);
					document.getElementById('timetxt').innerHTML=h+":"+m;					
				}							
				startTime();
				setInterval(function(){startTime()}, 1000);
			}
		</script>
	</head>
	<body onload="onload()">
		<div id="body">            
			<table class="top">
				<tr>
					<td class="l">
						<?
						echo "<span id='timetxt'>".date("H:i")."</span> ";
						?>												
						<a href="/stat/" style="color: white;text-decoration: none;">R</a>
					</td>
					<td class="c">
						<?
						echo get_cels($weather_data->main->temp);						
						foreach($weather_data->weather as $item);
						{
							echo "<img src='http://openweathermap.org/img/w/{$item->icon}.png'/> ";
							echo "<div>".str_replace(" ","<br/>", $item->description)."</div>";
						
						}					
						
						?>
					</td>
					<td class="r"><div><?=get_text('handicap')?>:</div>
                        <div class="handicap handicap_<?=$handicap_class?>"><?
                            echo format_time($handicap) . "&nbsp;";
							?></div>
                    </td>
				</tr>
			</table>			
		<table class="summary">
			<tr>
				<th><?=get_text('by_hours')?></th>
				<th><?=get_text('day')?></th>
				<th><?=get_text('week')?></th>
				<th><?=get_text('month')?></th>
			</tr>
			<tr>
				<td><img width="340" height="150" src="day.php?r=<?= microtime()?>"/></td>
				<td><img width="150" height="150" src="summary.php?time=day&r=<?= microtime()?>"/></td>
				<td><img width="150" height="150" src="summary.php?time=week&r=<?= microtime()?>"/></td>
				<td><img width="150" height="150" src="summary.php?time=month&r=<?= microtime()?>"/><br/></td>								
			</tr>
		</table>
		<img width="800" height="150" src="month.php?r=<?= microtime()?>"/><br/>
		
		
		
		</div>
	</body>
</html>
