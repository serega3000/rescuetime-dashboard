<?
if(! file_exists(__DIR__."/key.php"))
{
	echo "please rename key.example.php to key.php and fill your api key";
}

if(! file_exists(__DIR__."/vendor/autoload.php"))
{
	echo "please install dependencies using composer.";
}



?><html>
    <head>
        <script type="text/javascript;">
            setInterval(function(){                
                document.getElementById('iframe').src='stat.php?id=' + Math.random();                
            }, 300000);
        </script>            
    </head>
    <body>
        <iframe id="iframe" style="border:0;position:absolute;width: 100%;height: 100%;top:0;left:0;" src="stat.php"></iframe>
    </body>
</html>