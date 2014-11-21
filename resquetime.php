<?php

date_default_timezone_set('Europe/Moscow'); 

function resquetime_query(array $params)
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