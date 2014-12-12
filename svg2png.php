<?php 

$svg = $_GET["svg"];
$id = md5($svg);
if (!ISSET($svg)) {
	$id = $_REQUEST["id"];
	$svg = file_get_contents("https://s3.amazonaws.com/curliceu/" . $id . ".svg");
}

header('Content-Type: image/png');
$r = new Rsvg($svg);
$dimensions = $r->getDimensions();

//$s = new CairoImageSurface(CairoFormat::ARGB32, $dimensions['height'], $dimensions['width']);
$s = new CairoImageSurface(CairoFormat::ARGB32, $dimensions['width'], $dimensions['height']);
$c = new CairoContext($s);
$r->render($c);
$filename = "/tmp/" . uniqid($id) . ".php";
$s->writeToPng($filename);
echo file_get_contents($filename);
unlink($filename);

?>
