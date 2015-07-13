<!DOCTYPE html>
<html lang="cs" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->element(REDESIGN_PATH . 'default_head')?>
</head>
<body>
	<?php echo $this->element(REDESIGN_PATH . 'fb_root')?>
	<div class="container main-content">
		<?php echo $this->element(REDESIGN_PATH . 'header_row')?>
		<?php echo $this->element(REDESIGN_PATH . 'benefits_row')?>

        <div class="content row">
            <div class="module-page">
                <div class="aside" id="categories-navigation">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation"<?php echo ($categories_bothers_tab == 'categories' ? ' class="active"' : '')?>><a href="#categories" aria-controls="categories" role="tab" data-toggle="tab" class="categories-bothers-switch">Kategorie</a></li>
                        <?php if (false) { // UKRYTE KATEGORIE ZBOZI PODLE PRIZNAKU?>
                        <li role="presentation"<?php echo ($categories_bothers_tab == 'bothers' ? ' class="active"' : '')?>><a href="#bothers" aria-controls="bothers" role="tab" data-toggle="tab" class="categories-bothers-switch">Co vás trápí</a></li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade<?php echo ($categories_bothers_tab == 'categories' ? ' in active' : '')?>" id="categories">
                            <div id="categoriesMenu">
								<?php echo $this->element(REDESIGN_PATH . 'content_categories', array('categories' => $categories_menu))?>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade<?php echo ($categories_bothers_tab == 'bothers' ? ' in active' : '')?>" id="bothers">
                            <div id="bothersMenu">
                                <?php echo $this->element(REDESIGN_PATH . 'content_categories', array('categories' => $bothers_menu))?>
                            </div>
                        </div>
                    </div>
                    <div class="aside-links text-center">
                        <a href="http://obchody.heureka.cz/lekarnatypos-cz/recenze/" target="_blank">
                            <img src="/img/<?php echo REDESIGN_PATH?>heureka-overeno-zakazniky.png" alt="Heureka.cz - Ověřeno zákazníky" />
                        </a>
                        <a href="http://www.sukl.cz/lekarna/72995264000" target="_blank">
		    				<img src="/images/<?php echo REDESIGN_PATH?>sukl-overeni.png" alt="Ověřit lékárnu" title="Ověřit lékárnu" width="234px" height="195px"/> 
	    				</a>
                    </div>
                </div>
                <div class="content-section">
                	<?php if (isset($breadcrumbs)) { ?>
                	<div class="breadcrumbs">
                		<?php echo build_breadcrumbs($breadcrumbs); ?>
                	</div>
                	<?php }
						if ($this->Session->check('Message.flash')) {
		 					echo $this->Session->flash();
		 				}
		 				echo $content_for_layout;
		 			?>
                </div>
            </div>
			<?php echo $this->element(REDESIGN_PATH . 'module_newsletter')?>
			<?php echo $this->element(REDESIGN_PATH . 'facebook_news')?>
        </div>
        <?php echo $this->element(REDESIGN_PATH . 'quick_links_row')?>
    </div>
	<?php echo $this->element(REDESIGN_PATH . 'footer')?>
	<?php echo $this->element(REDESIGN_PATH . 'default_js')?>
	<div class="modal"><!-- Place at bottom of page --></div>
</body>
<?php echo $this->element('sql_dump')?>
</html>