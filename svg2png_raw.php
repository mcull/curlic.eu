<?php 

$svg = $_REQUEST["svg"];

$im = new Imagick();
$im->setBackgroundColor(new ImagickPixel('transparent'));
$im->readImageBlob($svg);


/*png settings*/
$im->setImageFormat("png24");
header('Content-Type: image/png');
echo $im;
$im->clear();
$im->destroy();

?>
