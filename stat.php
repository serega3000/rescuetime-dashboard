<?php
require 'rescuetime.php';

$last_monday = time();
if(date('w', $last_monday) != '1')
{
   
    $last_monday = strtotime('last monday');
}

$data = rescuetime_query(array(
	'restrict_kind' => 'productivity',
	'perspective' => 'rank',
	'restrict_end' => date('Y-m-d'),
	'restrict_begin' => date('Y-m-d', $last_monday),
));


$current_time = 0;

foreach($data->rows as $item_data)
{
	if($item_data[3] == 2)
	{
		$current_time += $item_data[1];
	}
}

$current_need = 60 * 60 * get_config('week_target');
$current_percent = min(100, floor($current_time / $current_need * 100));
$done = $current_time >= $current_need;

function format_time($t) // t = seconds, f = separator 
{
  return sprintf("%02d:%02d", floor($t/3600), ($t/60)%60);
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
					<td class="r"><div><?=get_text('work_per_week')?>:</div>
                        <?
                            echo format_time($current_time) . "&nbsp;";
                            if($done)
                            {
                                echo "OK!";
                            }
                            else
                            {
                                echo $current_percent . "%";
                            }
                        ?>
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
        <div class="bar"><div style="width:<?=$current_percent?>%"></div></div>
		<img width="800" height="150" src="month.php?r=<?= microtime()?>"/><br/>
		
		
		
		</div>
	</body>
</html>
