<?php 
$url = 'http://www.lekarnatypos.cz/suppliers/upload/4';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$st = curl_exec($ch);
curl_close($ch);
if (!empty($st)) {
	$headers = "Content-Type: text/plain; charset = \"UTF-8\";\n";
	$headers .= "Content-Transfer-Encoding: 8bit\n";
	$headers .= "\n";

	mail('brko11@gmail.com', 'LekarnaTypos CZ - problem pri natahovani Alliance1', $st, $headers);
}
?>
