<?php 
	$name_arr = explode('.', $name);
	$model = array_shift($name_arr);
	$input_id = $model . join('', array_map(array('Inflector', 'camelize'), $name_arr));
?>

<script type="text/javascript">
  $(function() {
    $('#<?php echo $input_id?>').combobox();
  });
</script>

<div class="ui-widget">
<?php
	echo $this->Form->input($name, array('label' => false, 'type' => 'select', 'options' => $options, 'empty' => $empty, 'div' => false))
?>
</div>