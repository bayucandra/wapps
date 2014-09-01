<?php
$doc=new DOMDocument();
$doc->loadHTML('
<html>
<head>
</head>
<body>
	<div>Div test wrapped</div>
	<p>p test wrapped</p>
</body>
</html>
');
$moc=new DOMDocument();
$bodys=$doc->getElementsByTagName('body')->item(0);
foreach($bodys->childNodes as $child){
	$moc->appendChild($moc->importNode($child,true));
}
echo $moc->saveHTML();

// foreach($bodys as $body){
// 	echo $doc->saveXML($doc->getElementsByTagName('body')->item(0));
// }
?>
