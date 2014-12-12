<?php 

$id = $_REQUEST["id"];
$svg = file_get_contents("https://s3.amazonaws.com/curliceu/" . $id . ".svg");

header('Content-Type: image/png');
$r = new Rsvg($svg);
$s = new CairoImageSurface(CairoFormat::ARGB32, 1000, 1000);
$c = new CairoContext($s);
$r->render($c);
$filename = "/tmp/" . $id . ".php";
$s->writeToPng($filename);
echo file_get_contents($filename);
unlink($filename);

?>
