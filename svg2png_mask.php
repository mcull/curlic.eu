<?php 
header('Content-Type: image/png');

$svg = file_get_contents("https://s3.amazonaws.com/curliceu/" . $_REQUEST["id"] . ".svg");

$png =  file_get_contents('http://curlic.eu/svg2png.php?svg=' . urlencode($svg));
$mask = new Imagick();
$mask->setBackgroundColor(new ImagickPixel('transparent'));
$mask->readImageBlob($png);

$h = $mask->getImageHeight();
$w = $mask->getImageWidth();

$back = new Imagick();
$back->newImage($w,$h,new ImagickPixel('transparent'));

$texture = new Imagick();
$texture->readImage('img/' . $_REQUEST['m'] .  '.png');

$back = $back->textureImage($texture);
$back->setimagematte(true);
$back->setImageFormat("png24");
$back->setimagematte(true);
$back->compositeImage($mask,Imagick::COMPOSITE_DSTIN, 0, 0);
echo $back;
$back->clear();
$back->destroy();
?>
