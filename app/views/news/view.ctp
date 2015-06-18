<h1><?php echo $actuality['News']['title']?></h1>
<?php
	$image = $image_path . '/' . $actuality['News']['image'];
	echo '<img src="/' . $image . '" alt="' . $actuality['News']['title'] . '" style="margin:0 5px 0 0" align="left"/>';
	echo $actuality['News']['text'];
?>