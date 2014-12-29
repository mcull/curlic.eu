<?php
require_once('../util.php');

$fonts = json_decode(file_get_contents('../fonts.json'),false);
$text = (ISSET($_REQUEST['text']) && strlen($_REQUEST['text']) > 0) ? $_REQUEST['text']: "Sample";
?>

<html>
	<head>
		<title>Fonts Cut Files</title>
		<link rel="stylesheet" href="../css/app.css" />
<link href='//fonts.googleapis.com/css?family=Rochester|Noto+Sans|Source+Code+Pro|Nixie+One' rel='stylesheet' type='text/css'>
		<style>
			.fontSample {
				margin: 10px;
				float:left;
				font-family: 'Noto Sans';
				border: 1px dotted #cccccc;
			}
		</style>
	<head>
	<body>
		<form action="" method="GET">
			Text to preview: <input type="text" name="text" placeholder="<?php echo $text; ?>"> <button type="submit" ">Submit</button>
		</form>
<?php
usort($fonts,"fontNameComparitor");
foreach ($fonts as $f) {
	echo "<div class='fontSample'>" . $f->ttfName . "<br>";
	echo "<img src='" . makeFontUrl($f,$text) . "'></div>" ;
}
?>

		<script src="../js/vendor/jquery.js"></script>
		<script src="../js/curliceu.js"></script>
	</body>
</html>