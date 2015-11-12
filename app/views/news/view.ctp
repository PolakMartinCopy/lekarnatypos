<h1><?php echo $actuality['News']['heading']?></h1>
<?php if (!empty($actuality['News']['subheading'])) { ?>
<h2 class="content-subheading"><?php echo $actuality['News']['subheading']?></h2>
<?php } ?>
<?php if (!empty($actuality['News']['perex'])) { ?>
<p><strong><?php echo $actuality['News']['perex'] ?></strong></p>
<?php } ?>
<?php
	$image = false;
	if (isset($actuality['News']['image'])) {
		$image_uri = $image_path . '/' . $actuality['News']['image'];
		if (file_exists($image_uri)) {
			$image_tag = '<img src="/' . $image_uri . '" alt="' . $actuality['News']['heading'] . '" style="margin:0 5px 0 0" align="left"/>';
		}
	}

	if ($image) {
		echo '<img src="/' . $image . '" alt="' . $actuality['News']['title'] . '" style="margin:0 5px 0 0" align="left"/>';
	}
	echo $actuality['News']['text'];
?>