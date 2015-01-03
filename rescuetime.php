<?php

function format_time($t) // t = seconds, f = separator 
{
	$sign = "";
	if($t < 0)
	{
		$t = abs($t);
		$sign = "-";
	}
	return $sign.sprintf("%02d:%02d", floor($t/3600), ($t/60)%60);
}

function get_config($key)
{
	static $config = null;
	
	if($config == null)
	{
		$config = include __DIR__."/config.php";
	}
	return $config[$key];
}

function get_text($alias)
{
	static $lang_data = null;
	
	if($lang_data == null)
	{
		$lang_data = include __DIR__."/lang.php";
	}
	return $lang_data[get_config('lang')][$alias];	
}

date_default_timezone_set(get_config('timezone')); 

function rescuetime_query(array $params)
{
	$params['key'] = include __DIR__."/key.php";
	$params['format'] = 'json';
	
	$query = "";
	foreach($params as $key=>$value) 
	{
		if(is_array($value)) 
		{
			foreach($value as $vkey=>$vvalue) 
			{
				$query .= sprintf('%s[]=%s&', $key, urlencode(stripslashes($vvalue)));
			}
		}
		else 
		{
			$query .= sprintf('%s=%s&',$key,urlencode(stripslashes($value)));
		}
	}
	$query = preg_replace('/\&$/','',$query);
	
	$url = 'https://www.rescuetime.com/anapi/data?'.$query;
	
	if(isset($_GET['rdebug']))
	{
		echo $url;die();
	}
	
	
	return json_decode(file_get_contents($url));

}