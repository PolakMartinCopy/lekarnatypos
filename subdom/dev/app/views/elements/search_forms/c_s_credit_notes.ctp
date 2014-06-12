<button id="search_form_show">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['CSCreditNote']) ){
		$hide = '';
	}
?>
<div id="search_form"<?php echo $hide?>>

	<?php echo $form->create('CSCreditNote', array('url' => array('controller' => $this->params['controller'], 'action' => 'index')))?>
	<table class="left_heading">
		<tr>
			<td colspan="6">Odběratel</td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $form->input('BusinessPartner.name', array('label' => false))?></td>
			<th>IČO</th>
			<td><?php echo $form->input('BusinessPartner.ico', array('label' => false))?></td>
			<th>DIČ</th>
			<td><?php echo $form->input('BusinessPartner.dic', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Ulice</th>
			<td><?php echo $this->Form->input('Address.street', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('Address.city', array('label' => false))?></td>
			<th>Okres</th>
			<td><?php echo $this->Form->input('Address.region', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="4">Faktura</td>
			<td colspan="2">Zboží</td>
		</tr>
		<tr>
			<th>Číslo dokladu</th>
			<td><?php echo $this->Form->input('CSCreditNote.code', array('label' => false))?></td>
			<th>Datum vystavení od</th>
			<td><?php echo $this->Form->input('CSCreditNote.date_of_issue_from', array('label' => false))?></td>
			<th>Datum vystavení do</th>
			<td><?php echo $this->Form->input('CSCreditNote.date_of_issue_to', array('label' => false))?></td>
		</tr>
			<th>Obchodník</th>
			<td><?php echo $this->Form->input('CSCreditNote.user_id', array('label' => false, 'empty' => true))?></td>
			<th>Datum splatnosti od</th>
			<td><?php echo $this->Form->input('CSCreditNote.due_date_from', array('label' => false))?></td>
			<th>Datum splatnosti do</th>
			<td><?php echo $this->Form->input('CSCreditNote.due_date_to', array('label' => false))?></td>
		<tr>
			<th>Kód skupiny</th>
			<td><?php echo $this->Form->input('Product.group_code', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $this->Form->input('Product.vzp_code', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => $this->params['controller'], 'reset' => true)) ?>
			</td>
		</tr>
	</table>
	
	<?php
		echo $form->hidden('CSCreditNote.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
	
</div>

<script>
	$("#search_form_show").click(function () {
		if ($('#search_form').css('display') == "none"){
			$("#search_form").show("slow");
		} else {
			$("#search_form").hide("slow");
		}
	});

	$(function() {
		var model = 'CSCreditNote';
		var dateOfIssueFromId = model + 'DateOfIssueFrom';
		var dateOfIssueToId = model + 'DateOfIssueTo';
		var datesOfIssue = $('#' + dateOfIssueFromId + ',#' + dateOfIssueToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dateOfIssueFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				datesOfIssue.not( this ).datepicker( "option", option, date );
			}
		});
		
		var dueDateFromId = model + 'DueDateFrom';
		var dueDateToId = model + 'DueDateTo';
		var dueDates = $('#' + dueDateFromId + ',#' + dueDateToId).datepicker({
			changeMonth: false,
			numberOfMonths: 1,
			onSelect: function( selectedDate ) {
				var option = this.id == dueDateFromId ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dueDates.not( this ).datepicker( "option", option, date );
			}
		});
	});
	$( "#datepicker" ).datepicker( $.datepicker.regional[ "cs" ] );
</script>