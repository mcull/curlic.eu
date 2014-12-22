<?php

function getParam($paramName, $defaultVal) {
	return (ISSET($_REQUEST[$paramName])) ? $_REQUEST[$paramName] : $defaultVal; 
}

$font = getParam("font","Arial");
$spacing = getParam("spacing","0");
$text = getParam("text","No text supplied");

$svg = file_get_contents('template.svg');
$svg = str_replace("{{font}}",getParam("font","Arial"),$svg);
$svg = str_replace("{{spacing}}",getParam("spacing","0"),$svg);
$svg = str_replace("{{text}}",getParam("text","Sample"),$svg);
$svg = str_replace("{{stroke}}",getParam("stroke","0"),$svg);
$svg = str_replace("{{fontSize}}",getParam("size","120"),$svg);
$svg = str_replace("{{top}}",getParam("t","100"),$svg);
$svg = str_replace("{{left}}",getParam("l","45"),$svg);

//write file 
$id = uniqid();
$tempSvg = "/tmp/" . $id . ".svg";
$tempPng = "/tmp/" . $id . ".png";
file_put_contents($tempSvg, $svg, LOCK_EX);

$svgConvert = "java -Djava.awt.headless=true -jar /home/ubuntu/batik-1.7/batik-rasterizer.jar -dpi 100 -w 600 -h 175 " . $tempSvg;

exec($svgConvert);
$png = file_get_contents($tempPng);
header('Content-Type: image/png');
unlink($tempSvg);
unlink($tempPng);
echo $png;


?>
