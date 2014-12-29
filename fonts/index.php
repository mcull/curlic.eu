<?php
require_once('../util.php');

$fonts = json_decode(file_get_contents('../fonts.json'),false);
?>

<html>
	<head>
		<title>Fonts Cut Files</title>
		<style>
			img {
				margin: 10px;
				float:left;
			}
		</style>
	<head>
	<body>
<?php
foreach ($fonts as $f) {
	echo "<img src='" . makeFontUrl($f,"sample") . "'>" ;
}
?>

		<script src="js/vendor/jquery.js"></script>
		<script src="js/curliceu.js"></script>
	</body>
</html>