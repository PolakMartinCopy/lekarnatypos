<div id="search_form_t_s_visits">
	<?php echo $this->Form->create('TSVisit')?>
	<table class="tabulka">
		<tr>
			<th style="width:25%">Od</th>
			<td style="width:25%"><?php echo $this->Form->input('TSVisitForm.TSVisit.from', array('label' => false, 'type' => 'text'))?></td>
			<th style="width:25%">Do</th>
			<td style="width:25%"><?php echo $this->Form->input('TSVisitForm.TSVisit.to', array('label' => false, 'type' => 'text'))?></td>
		</tr>
	</table>
	<?php
		echo $this->Form->submit('Vyhledat', array('div' => false));
		echo $form->hidden('TSVisitForm.TSVisit.search_form', array('value' => true));
		echo $form->end();
	?>
</div>

<script>
	$(function() {
		var dateFromId = 'TSVisitFormTSVisitFrom';
		var dateToId = 'TSVisitFormTSVisitTo';
		var dates = $('#' + dateFromId + ',#' + dateToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dateFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>