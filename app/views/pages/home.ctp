<h1>Vaše oblíbená lékárna v centru Brna</h1>
<?php if (isset($banner_image)) { ?>
<div class="homepage-banner" style="text-align:center">
<?php
$img = '<img src="/' . $banner_image . '" />';
if (isset($banner_url) && !empty($banner_url)) {
	echo $this->Html->link($img, $banner_url, array('escape' => false));
} else {
	echo $img;
} ?>
</div>
<?php } ?>

<?php echo $this->element(REDESIGN_PATH . $listing_style); ?>