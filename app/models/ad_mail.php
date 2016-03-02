<?php 
class AdMail extends AppModel {
	var $name = 'AdMail';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('AdMailTemplate', 'Customer');
	
	function init($customerId) {
		$adMailTemplate = $this->AdMailTemplate->findByType($this->mailTemplateType);
		$save = array(
			$this->name => array(
				'sent' => false,
				'opened' => false,
				'ad_mail_template_id' => $adMailTemplate['AdMailTemplate']['id'],
				'customer_id' => $customerId
			)
		);

		$this->create();
		return $this->save($save);
	}
	
	function subject() {
		$mailTemplate = $this->AdMailTemplate->findByType($this->mailTemplateType);
	
		if (empty($mailTemplate)) {
			return false;
		}
		return $mailTemplate['AdMailTemplate']['subject'];
	}
	
	function bodyAlternative() {
		return 'http://www.lekarnatypos.cz/';
	}
	
	function setSent($id) {
		return $this->setAttribute($id, 'sent', true);
	}
	
	function setOpened($id) {
		return $this->setAttribute($id, 'opened', true);
	}
	
	function getProductsBox($products, $campaignName) {
		$res = '';
		foreach ($products as $product) {
			$res .= '
	<tr>
	    <td valign="middle" style="padding: 3px 5px 3px 5px"><a href="http://www.lekarnatypos.cz/' . $product['Product']['url'] . '?utm_source=newsletter&utm_medium=email&utm_campaing=' . $campaignName . '" target="_blank"><img src="http://www.lekarnatypos.cz/product-images/small/' . $product['Image']['name'] . '" width="70"/></a></td>
	    <td valign="middle" style="padding:3px 5px 3px 0"><a href="http://www.lekarnatypos.cz/' . $product['Product']['url'] . '?utm_source=newsletter&utm_medium=email&utm_campaing=' . $campaignName . '" style="color: #63af29;" target="_blank">' . $product['Product']['name'] . '</a></td>
	    <td align="right" valign="middle" style="padding:3px 5px">' . $product['Product']['price'] . '&nbsp;Kč</td>
	</tr>';
		}
		return $res;
	}
	
	function sendMail($subject, $body, $bodyAlternative, $email) {
		App::import('Vendor', 'MailKomplet', array('file' => 'mail_komplet.php'));
		$mailKomplet = &new MailKomplet;
	
		$mailKomplet->login();
	
		// dispatcherId je pevne dano
		$dispatcherId = 1677;
		// poslu uzivateli / zakaznikovi pres mail komplet
		$mailKompletSent = $mailKomplet->sendMail(CUST_NAME, CUST_MAIL, $email, $subject, $body, $bodyAlternative, $dispatcherId);
	
		return $mailKompletSent;
	}
	
	function isOpened($cryptId, $cryptEmail = null) {
		$url = 'http://www.lekarnatypos.cz/files/ad_mail_templates_images/lekarnatypos-logo.png';
		$image = download_url($url);
		
		if ($cryptEmail) {
			$email = urldecode(Security::cipher($cryptEmail, Configure::read('Security.salt')));
			$adminEmails = array(
				'brko11@gmail.com',
				'nejedly.lukyn@gmail.com',
				'martin@drdla.eu'
			);
			// pokud jsem se sem dostal z administratorskeho emailu (notifikace), tak neoznacuju email jako otevreny
			if (in_array($email, $adminEmails)) {
				die();
			}
		}
		$id = urldecode(Security::cipher($cryptId, Configure::read('Security.salt')));
		$this->setOpened($id);
		
		return $image;
	}
}
?>