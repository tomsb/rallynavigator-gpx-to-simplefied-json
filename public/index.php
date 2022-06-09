<?php

require_once '../vendor/autoload.php';

use Mtownsend\XmlToArray\XmlToArray;

$uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/';
$uploadfile = $uploaddir . 'input.gpx';

if (isset($_FILES["userfile"]) && file_exists($_FILES["userfile"]["tmp_name"]) && move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {

	$xml = file_get_contents($uploadfile);

	@$array = XmlToArray::convert($xml);

	if (is_array($array) && ! empty($array) && isset($array['wpt'])) {
		unset($array['trk']);

		foreach ($array['wpt'] as &$tulip) {
			unset($tulip['ele']);
		}

		$json = json_encode($array);

		$file_url = $_SERVER['DOCUMENT_ROOT'] . '/output.json';

		$fp = fopen($file_url, 'w');
		fwrite($fp, $json);
		fclose($fp);

		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=" . basename($file_url));
		readfile($file_url);
		die();
	}

}

?>
<!DOCTYPE html>
<html>
<body style="margin: 0 auto; width: 50%">

<h1>Convert rallynavigator.com GPX to simplified JSON</h1>
<hr>
<ul>
	<li>remove <i>trk</i> data (GPS coordinates)</li>
	<li>remove <i>ele</i> data from tulip (redundant information)</li>
	<li>remove <i>@attributes</i> data from tulip (GPS coordinates)</li>
</ul>
<hr>
<br>
<form enctype="multipart/form-data" action="/" method="POST">
	Rallynavigator.com GPX file: <input name="userfile" type="file"/>
	<br>
	<br>
	<input type="submit" value="Convert & download"/>
</form>

</body>
</html>

