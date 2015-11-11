<div class="modal-backdrop subscribe-modal in" modal-backdrop="" style="z-index: 1040;"></div>
<div tabindex="-1" role="dialog" class="modal subscribe-modal in" modal-window="" index="0" animate="animate" style="z-index: 1050;">
    <div class="modal-dialog modal-md">
        <div class="modal-content subscribe-modal-content" modal-transclude="">
            <div class="modal-header text-right">
            	<button aria-label="Close" data-dismiss="modal" class="close" type="button" onclick="popupAlreadyShown();"><span aria-hidden="true">×</span></button>
                <h3 class="modal-title">
                    Získejte <a href="/kazdy-mesic-voucher-pravidla-souteze" style="color: #e30780;" target="_blank"><span style="color: #e30780">každý měsíc poukaz na 1000 Kč</span></a><br />na nákup v naší <span style="color: #63af29">Lékárně Typos</span>
                </h3>
            </div>
            <div class="modal-body">
            	<?php echo $this->Form->create('NewsletterApplicant', array('id' => 'NewsletterApplicantAddForm', 'action' => '#'))?>
                <form method="post" action="#">
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-9">
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label>Moje e-mailová adresa:</label>
                                    <?php echo $this->Form->input('NewsletterApplicant.email', array('label' => false, 'class' => 'form-control', 'required' => true))?>
                                    <!-- <input class="form-control" name="email" type="text" required /> -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4 col-xs-6">
                                    <label>Moje jméno:</label>
                                    <?php echo $this->Form->input('NewsletterApplicant.first_name', array('label' => false, 'class' => 'form-control', 'required' => true))?>
                                    <!-- <input class="form-control" name="firstName" type="text" required /> -->
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                    <label>Moje příjmení:</label>
                                     <?php echo $this->Form->input('NewsletterApplicant.last_name', array('label' => false, 'class' => 'form-control', 'required' => true))?>
                                    <!-- <input class="form-control" name="surName" type="text" required /> -->
                                </div>
                                <div class="col-sm-4 col-xs-12">
                                    <label>&nbsp;</label>
                                    <?php echo $this->Form->hidden('NewsletterApplicant.modal_window_identifier', array('value' => 'kazdy-mesic-poukaz-1'))?>
                                    <button type="button" class="btn btn-primary" id="FormSubmit">Chci vyhrát</button>
                                </div>
                            </div>
                            <p class="small">
                                Na zadanou e-mailovou adresu Vám budeme zasílat aktuální nabídky, akce, slevy, slevové kupóny a informace o dopravě zdarma. Z těchto newsletteru se můžete samozřejmě kdykoliv odhlásit kliknutím na odkaz v patičce každého e-mailu.
                            </p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer text-right">
                <button type="button" class="btn btn-link btn-xs" data-dismiss="modal" onclick="popupAlreadyShown();">Ne, děkuji</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var popupID;
    function showPopup() {
    	popupID = window.setTimeout(checkCookies, 10000);
    }
    function checkCookies() {
        if (document.cookie.indexOf("subscribe-popup") < 0 && window.location.href != 'http://www.lekarnatypos.cz/kazdy-mesic-voucher-pravidla-souteze') {
            $("body").addClass("modal-open");
            $('.subscribe-modal').fadeIn(500);
        }
    }
    function popupAlreadyShown() {
        var date = new Date();
        date.setFullYear(date.getFullYear() + 1);
        document.cookie = 'subscribe-popup=1; path=/; expires=' + date.toGMTString();
        $("body").removeClass("modal-open");
        $('.subscribe-modal').fadeOut(250);
    }

    $('#FormSubmit').click(function(e) {
        e.preventDefault();
        
        var firstName = $('#NewsletterApplicantFirstName').val();
        var lastName = $('#NewsletterApplicantLastName').val();
		var email =  $('#NewsletterApplicantEmail').val();
        var modalWindowIdentifier = $('#NewsletterApplicantModalWindowIdentifier').val();

		// validace neprazdnosti emailu
		// smazu message, pokud byla zobrazena
		$('#NewsletterApplicantEmail').next('.error-message').remove();

        $.ajax({
			url: '/newsletter_applicants/ajax_add',
			type: 'post',
			dataType: 'json',
			data: {
				email: email,
				firstName: firstName,
				lastName: lastName,
				modalWindowIdentifier: modalWindowIdentifier
			},
			success: function(data) {
				if (data.success) {
					popupAlreadyShown();
					alert(data.message);
				} else {
					var messagePrefix = '<div class="error-message">';
					var messageSuffix = '</div>';
					
					validationErrors = data.message;
					$.each(validationErrors, function(index, message) {
						switch(index) {
							case 'email': element = '#NewsletterApplicantEmail'; break;
						}
						$(element).after(messagePrefix + message + messageSuffix);
					});
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('chyba pri zpracovani');
			}
        });
    });
</script>