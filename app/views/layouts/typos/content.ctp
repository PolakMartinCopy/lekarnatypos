<!DOCTYPE html>

<html lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
</head>
<body>
    <div class="container">
		<?php
			echo $this->element(REDESIGN_PATH . 'header');
			echo $this->element(REDESIGN_PATH . 'nav');
		?>

        <div class="main-content clearfix">
			<?php echo $this->element(REDESIGN_PATH . 'left_sidebar')?>
            <div class="col-right">
			<?php
			echo $this->element(REDESIGN_PATH . 'breadcrumbs');
			if ($this->Session->check('Message.flash')) {
				echo $this->Session->flash();
			}
			echo $content_for_layout;
			?>
            </div>
        </div>
    </div>
    <?php
    	echo $this->element(REDESIGN_PATH . 'footer');
    	echo $this->element(REDESIGN_PATH . 'default_js');
    ?>
</body>
</html>
<?php echo $this->element('sql_dump')?>