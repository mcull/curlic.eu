<?php 

$svg = file_get_contents("https://s3.amazonaws.com/curliceu/" . $_REQUEST["id"] . ".svg");
/*
$mask = new Imagick();
$mask->setBackgroundColor(new ImagickPixel('transparent'));
$mask->readImageBlob($svg);

$back = new Imagick('img/gold_glitter.png');
$back->setimagematte(true);
/*png settings*/
/*
$back->setImageFormat("png24");
$back->setimagematte(true);
$back->compositeImage($mask,Imagick::COMPOSITE_DSTIN, 0, 0);
header('Content-Type: image/png');
echo $back;
$back->clear();
$back->destroy();
*/

header('Content-Type: image/png');
//header('Content-Type: text/html');
$r = new Rsvg($svg);
//var_dump($r->getDimensions());
$s = new CairoImageSurface(CairoFormat::ARGB32, 1000, 1000);
$c = new CairoContext($s);
$r->render($c);
$s->writeToPng("/tmp/xxx.png");
echo file_get_contents("/tmp/xxx.png");

?>
