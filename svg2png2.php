<?php

$svgConvert = "java -Djava.awt.headless=true -jar /home/ubuntu/batik-1.7/batik-rasterizer.jar /var/www/curlic.eu/fontTest.svg -d /tmp/1234.png";

exec($svgConvert);

header('Content-Type: image/png');
echo file_get_contents("/tmp/1234.png");

?>
