<div class="search-box">
	<?php echo $this->Form->create('Search', array('url' => array('controller' => 'searches', 'action' => 'do_search'), 'encoding' => false, 'type' => 'get'));?>
    <div class="input-group">
		<?php echo $this->Form->input('Search.q', array('label' => false, 'class' => 'form-control', 'type' => 'text', 'placeholder' => (isset($this->data['Search']['q']) ? $this->data['Search']['q'] : 'zadejte dotaz'), 'div' => false));?>
        <span class="input-group-btn">
        	<?php echo $this->Form->submit('Vyhledat', array('class' => 'search_submit', 'div' => false, 'class' => 'btn btn-success'));?>
        </span>
    </div>
    <?php echo $this->Form->end(); ?>
</div>