<div class="facebook-news-section">
    <div class="module-facebook hidden-xs col-md-6 col-lg-6">
        <div class="fb-page" data-href="https://www.facebook.com/lekarnatypos" data-width="500" data-height="300" data-hide-cover="false" data-show-facepile="true" data-show-posts="true">
            <div class="fb-xfbml-parse-ignore">
                <blockquote cite="https://www.facebook.com/lekarnatypos"><a href="https://www.facebook.com/lekarnatypos">Lékárna Typos</a></blockquote>
            </div>
        </div>
    </div>
    <div class="module-articles col-md-6 col-lg-6">
        <h3>Nejnovější články</h3>
        <?php if (isset($newest_actualities) && !empty($newest_actualities)) { ?>
        <ul class="article-list">
        	<?php foreach ($newest_actualities as $actuality) {
				$link = array('controller' => 'news', 'action' => 'view', $actuality['News']['id']);
        	?>
            <li>
            	<?php echo $this->Html->link('<img src="' . $actuality['News']['image'] . '" />', $link, array('escape' => false))?>
            	<?php echo $this->Html->link($actuality['News']['heading'], $link, array('class' => 'title'))?>
                <p><?php echo $actuality['News']['perex']?></p>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>
        <p class="text-right">
        	<?php echo $this->Html->link('Archiv článků', array('controller' => 'news', 'action' => 'index'), array('class' => 'btn btn-success'))?>
        </p>
    </div>
</div>