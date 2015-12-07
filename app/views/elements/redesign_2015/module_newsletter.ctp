<div class="module-newsletter">
    <div class="text-center newsletter">
        <ul>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
        <div class="newsletter-box">
            <div class="row">
                <div class="col-sm-12">
                    <h3>Nepropásněte už žádnou akční nabídku!</h3>
                    <p>Nechte nám svůj e-mail a buďte první, kdo bude vědět o všech slevách, výprodejích a akčních nabídkách.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <!-- Formular pro prihlaseni prijemce - Zacatek -->
                    <script type="text/javascript">
                        function VerifyConfirmation() {
                            var elm = document.getElementById("email");
                            if (elm.value == null || elm.value == "") {
                                alert("E-mail musí být vyplněn.");
                                return false;
                            }
                            return true;
                        }
                    </script>
					<form method="post" class="form-inline" action="http://www.mail-komplet.cz/m/?action=subscribe&data=Ss4lfHFa9YStcTiY3tmnDIg2iimL2LzTvJVI789LPXKaxYtOZpRFYDvMx893zo5Lzt0we6RTMb92KHb9d2%2fTnS9MEx6IcVQkDM5fg%2f%2bB8HxDwWbdp%2fd6eGZYMY5XfWfW"
                          onsubmit="return VerifyConfirmation();">
                        <input type="hidden" name="targetUrl" value="http://<?php echo $_SERVER['HTTP_HOST']?>/pages/prihlaseni-k-odberu-newsletteru/" />
                    
                        <div class="input-group">
                            <input type="email" size="30" class="form-control" name="email" id="email" placeholder="vas.email@domena.cz">
                            <span class="input-group-btn">
                                <button type="submit" name="subscribe" class="btn btn-warning">Chci být informován</button>
                            </span>
                        </div>

                    </form>
                    <!-- Formular pro prihlaseni prijemce - Konec -->
                </div>
            </div>
        </div>
    </div>
</div>