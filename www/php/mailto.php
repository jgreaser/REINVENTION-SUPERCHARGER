<?php

$to = 'joegreaser+4zs8ycbrixswmrveqhjq@boards.trello.com';
//$to = 'jgreaser@gmail.com';


//$to = $_GET["receiver"];
$header = 'From: FLVS';


$subject = $_GET["title"]." #".$_GET["category"];
$body = "**Inspiration**\n".$_GET["inspiration"]."\n\n"."**Solution**\n".$_GET["solution"]."\n\n"."**Problem**\n".$_GET["problem"]."\n\n**Contact**\n".$_GET["contact"];


if (mail($to, $subject, $body, $header)) {
	echo($body);
} else {
	echo('Message delivery failed...');
}
?>

