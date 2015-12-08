<div class="module-we-call-you">
    <div class="we-call-you">
        <div class="row">
            <div class="col-md-6">
                <h3>Nenašli jste u nás svůj oblíbený produkt?</h3>
                <p>Potřebujete poradit? Nechte nám svoje telefonní číslo a my Vám zavoláme.</p>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-5">
                <form method="post" class="form-inline" action="#" id="WeCallYouForm">
                    <div class="input-group" id="ContactLine">
                        <input size="30" class="form-control" id="Contact">
                        <span class="input-group-btn">
                            <button type="submit" name="subscribe" class="btn btn-warning">Zavolejte mi</button>
                        </span>
                    </div>
                </form>
                <p class="small">
                    Vaše telefonní číslo bude u nás v bezpečí. Nikdy ho nepředáme třetím stranám
                    ani jej nepoužijeme pro jiné účely než je tento.
                </p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#WeCallYouForm').submit(function(e) {
	    e.preventDefault();
	    
	    var contact = $('#Contact').val();
	
	 	// validace neprazdnosti, smazu message, pokud byla zobrazena
		$('#ContactLine').next('.error-message').remove();
	
		contactValid = true;
	
		var messagePrefix = '<div class="error-message">';
		var messageSuffix = '</div>';
		
		if (contact == '') {
			$('#ContactLine').after(messagePrefix + 'Zadejte Váš telefon nebo emailovou adresu.' + messageSuffix);
		} else {
			$.ajax({
				url: '/tools/ajax_we_call_you_request',
				type: 'post',
				dataType: 'json',
				data: {
					contact: contact,
				},
				success: function(data) {
					if (data.success) {
						messagePrefix = '<div class="success-message">';
					}
					$('#ContactLine').after(messagePrefix + data.message + messageSuffix);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert('chyba pri zpracovani');
				}
		    });
		}
	});
});
</script>