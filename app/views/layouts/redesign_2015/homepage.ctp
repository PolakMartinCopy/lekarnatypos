<!DOCTYPE html>
<html lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
</head>
<body class="indexpage" onload="showPopup();">
	<?php echo $this->element(REDESIGN_PATH . 'fb_root')?>
    <div class="container main-content">
		<?php echo $this->element(REDESIGN_PATH . 'header_row')?>
		<?php echo $this->element(REDESIGN_PATH . 'benefits_row')?>

        <div class="content row">
        	<?php if (isset($breadcrumbs)) { ?>
            <div class="breadcrumbs">
            <?php echo build_breadcrumbs($breadcrumbs); ?>
			</div>
			<?php }
 			// flash, pokud neni definovano, ze se bude zobrazovat nekde uvnitr stranky
	 			if ($this->Session->check('Message.flash')) {
 					$flash = $this->Session->read('Message.flash');
 					if (!isset($flash['params']['type']) || $flash['params']['type'] != 'in_page') {
	 					echo $this->Session->flash();
 					}
 				}

	 			echo $content_for_layout;

				echo $this->element(REDESIGN_PATH . 'module_newsletter');
				echo $this->element(REDESIGN_PATH . 'facebook_news');
			?>
        </div>
		<?php echo $this->element(REDESIGN_PATH . 'quick_links_row')?>
    </div>
	<?php echo $this->element(REDESIGN_PATH . 'footer')?>
	<?php echo $this->element(REDESIGN_PATH . 'default_js')?>
	<?php echo $this->element(REDESIGN_PATH . 'modal-newsletter-subscribe')?>
	<div class="modal-order"><!-- Place at bottom of page --></div>
</body>
<?php echo $this->element('sql_dump')?>
</html>
