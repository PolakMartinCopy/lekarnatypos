<div id="search">
<!-- 	<a href="#">Lorem ispum</a>
	<a href="#">Kuldere mutes</a> -->
	<?php echo $form->create('Search', array('url' => '/vyhledavani-produktu', 'type' => 'get', 'id' => 'search_form', 'encoding' => false)) ?>
		<div class="pair">
			Vyhledávání:
			<?php echo $this->Form->input('Search.q', array('label' => false, 'type' => 'text', 'class' => 'text', 'placeholder' => 'Zde zadejte hledaný výraz', 'div' => false))  ?>
			<?php echo $this->Form->hidden('sorting')?>
			<?php echo $this->Form->hidden('paging')?>
			<button name="send"> </button>
		</div>
	<?php echo $form->end() ?>
</div>