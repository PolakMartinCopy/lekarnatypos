<?php
/* SVN FILE: $Id: bootstrap.php 6311 2008-01-02 06:33:52Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.10.8.2117
 * @version			$Revision: 6311 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-01 22:33:52 -0800 (Tue, 01 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */
function strip_diacritic($text, $strip_dot = true) {
	$text = trim($text);
	
	$text = str_replace(",", "-", $text); // carky
	$text = str_replace("(", "", $text); // leve zavorky
	$text = str_replace(")", "", $text); // prave zavorky
	$text = str_replace("&amp;", "a", $text); // prave zavorky
	$text = str_replace("&", "a", $text); // prave zavorky
	$text = str_replace("?", "", $text); // prave zavorky
	$text = str_replace("%", "", $text); // procenta

	$text = str_replace('´', '', $text); // apostrof
	$text = str_replace("'", "", $text); //apostrof
	$text = str_replace('"', '', $text); //uvozovky
	$text = str_replace("/", "", $text); // lomitko
	$text = str_replace("+", "-", $text); // plus
	$text = str_replace('!', '', $text); // vykricnik
	$text = str_replace('™', '', $text); // trademark
	
	if ($strip_dot) {
		$text = str_replace(".", "", $text); // tecka
	}

	// odstranim pismena s diakritikou
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', 'Ř'=>'R', 'ř'=>'r', 'Ť'=>'T', 'ť'=>'t', 'Ě'=>'E', 'ě'=>'e',
    	'Ň'=>'N', 'ň'=>'n', 'ú'=>'u', 'Ú'=>'U', 'ů'=>'u', 'Ů'=>'U', 'ď'=>'d', 'Ď'=>'d', 'ü'=>'u'
    );
    $text = strtr($text, $table);

	// mezery nahradim pomlckama (jedna pomlcka i za vice mezer)
	$text = preg_replace('/\s+/', '-', $text);

	// hodim text na mala pismena
	$text = strtolower($text);
	
	// odstranim vic pomlcek za sebou
	while (preg_match('/--/', $text)) {
		$text = preg_replace('/--/', '-', $text);
	}

	return $text;
}

function cz_date_time($datetime, $date_separator = '-') {
	$dt = strtotime($datetime);
	$dt = strftime('%d' . $date_separator . '%m' . $date_separator . '%Y %H:%M', $dt);
	return $dt;
}

function cz_date($date, $date_separator = '-') {
	$dt = strtotime($date);
	$dt = strftime('%d' . $date_separator . '%m' . $date_separator . '%Y', $dt);
	return $dt;
}

/** Kontrola e-mailové adresy
* @param string $email e-mailová adresa
* @return bool syntaktická správnost adresy
* @copyright Jakub Vrána, http://php.vrana.cz
*/
function valid_email($email) {
    $atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]';
    $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';
    return eregi("^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$", $email);
}

function eval_expression($expression){
	// uprava pole s cenou, aby se mohly vkladat vyrazy
	$code = "\$number = (" . $expression . ") * 1;";
	eval($code);
	return floor($number);
}

function resize($filename, $max_x = 100, $max_y = 100) {
	// musim si u kazdeho obrazku zjistit jeho rozmery
	$i = getimagesize($filename);
	
	if ( $max_x < $i[0] OR $max_y < $i[1]){
		// vim ze rozmer je vetsi nez povolene rozmery
		if ( $max_x < $i[0] ){
			// zmensim ho nejdriv po ose X
			$xratio = $i[0] / $max_x;
			$i[0] = $max_x;
    		$i[1] = round($i[1] / $xratio);
		}
		
		if ( $max_y < $i[1] ){
			// pokud to jeste porad nestacilo po ose X,
			// zmensim si ho po ose Y
			$yratio = $i[1] / $max_y;
			$i[1] = $max_y;
			$i[0] = round($i[0] / $yratio);
		}
	}
	
	return array($i[0], $i[1]);
}

function cz2db_datetime($datetime) {
	$datetime = explode(' ', $datetime);
	$date = $datetime[0];
	$time = $datetime[1];

	$date = explode('.', $date);
	if (strlen($date[0]) == 1) {
		$date[0] = '0' . $date[0];
	}
	if (strlen($date[1]) == 1) {
		$date[1] = '0' . $date[1];
	}
	$date = $date[2] . '-' . $date[1] . '-' . $date[0];

	$datetime = $date . ' ' . $time;
	return $datetime;
}

function cz2db_date($date) {
	$date = explode('.', $date);
	if (strlen($date[0]) == 1) {
		$date[0] = '0' . $date[0];
	}
	if (strlen($date[1]) == 1) {
		$date[1] = '0' . $date[1];
	}
	$date = $date[2] . '-' . $date[1] . '-' . $date[0];
	return $date;
}

function json_encode_result($result) {
	if (!function_exists('json_encode')) {
		App::import('Vendor', 'Services_JSON', array('file' => 'JSON.php'));
		$json = &new Services_JSON();
		return $json->encode($result);
	}
	
	return json_encode($result);
}

function format_price($price) {
	return number_format($price, 0, ',', '.') . ' CZK';
}

function front_end_display_price($price, $decimals = 0) {
	return number_format($price, $decimals, ',', ' ');
}

function download_url($url = null) {
	if ($url) {
		$content = false;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	return false;
}

function download_url_like_browser($url = null) {
	$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
	if ($url) {
		$content = false;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}
	return false;
}

function get_topvet_xml_products_list($file_url, $id_xpath, $title_xpath) {
	// nactu xml
	if (!$xml = download_url($file_url)) {
		trigger_error('Nepodarilo se natahnou ulozeny xml ze souboru ' . $file, E_USER_ERROR);
	}
	
	
	// vyparsuju pole nazvu produktu z feedu, indexovanych indexem produktu na syncaru
	$xml_document = new SimpleXMLElement($xml);
	$ids = $xml_document->xpath($id_xpath);
	$names = $xml_document->xpath($title_xpath);
	
	if (count($ids) != count($names)) {
		debug(count($ids));
		debug(count($names));
		return false;
	}
	
	$xml_products_list = array();
	foreach ($ids as $index => $id) {
		$id = $id->__toString();
		$name = $names[$index]->__toString();
		$xml_products_list[$id] = $name;
	}
	
	return $xml_products_list;
}

function get_syncare_xml_products_list($file_url, $id_xpath, $title_xpath, $size_xpath) {
	// nactu xml
	if (!$xml = download_url($file_url)) {
		trigger_error('Nepodarilo se natahnou ulozeny xml ze souboru ' . $file, E_USER_ERROR);
	}
	
		
	// vyparsuju pole nazvu produktu z feedu, indexovanych indexem produktu na syncaru
	$xml_document = new SimpleXMLElement($xml);
	$ids = $xml_document->xpath($id_xpath);
	$names = $xml_document->xpath($title_xpath);
	$sizes = $xml_document->xpath($size_xpath);
		
	if (count($ids) != count($names) || count($ids) != count($sizes)) {
		debug(count($ids));
		debug(count($names));
		debug(count($sizes));
		return false;
	}
		
	$xml_products_list = array();
	foreach ($ids as $index => $id) {
		$id = $id->__toString();
		$name = $names[$index]->__toString() . ' - ' . $sizes[$index]->__toString();
		$xml_products_list[$id] = $name;
	}
	
	return $xml_products_list;
}

/*
 * zjisti, jestli existuje v SimpleXMLElementu potomek s danym nazvem
 */
function simpleXMLChildExists($simpleXMLElement, $childName) {
	$namespaces = $simpleXMLElement->getNameSpaces(true);
	// zkousim, jestli tam je povinny atribut
	// musim zjistit, jestli nazev elementu s hodnotou atributu neobsahuje namespace
	if (preg_match('/([^:]+):(.*)/', $childName, $matches)) {
		$namespace_name = $matches[1];
		$element_name = $matches[2];
		$namespace = $simpleXMLElement->children($namespaces[$namespace_name]);
	
		return $namespace->$element_name;
	}
	return $simpleXMLElement->{$childName};
}

function simpleXMLChildValue($simpleXMLElement, $childName) {
	$namespaces = $simpleXMLElement->getNameSpaces(true);
	// zkousim, jestli tam je povinny atribut
	// musim zjistit, jestli nazev elementu s hodnotou atributu neobsahuje namespace
	if (preg_match('/([^:]+):(.*)/', $childName, $matches)) {
		$namespace_name = $matches[1];
		$element_name = $matches[2];
		$namespace = $simpleXMLElement->children($namespaces[$namespace_name]);
	
		return $namespace->$element_name->__toString();
	}
	return $simpleXMLElement->{$childName}->__toString();
}

// sestavi HTML breadcrumbs linku z pole
function build_breadcrumbs($breadcrumbs_arr) {
	$breadcrumbs_link = array();
	$i = 0;
	foreach ($breadcrumbs_arr as $breadcrumbs_arr_item) {
		$breadcrumb_link = '<a href="' . $breadcrumbs_arr_item['href'] . '">' . $breadcrumbs_arr_item['anchor'] . '</a>';
		// posledni prvek nebude odkaz
		if ($i == (count($breadcrumbs_arr) - 1)) {
			$breadcrumb_link = $breadcrumbs_arr_item['anchor'];
		}
		$breadcrumbs_link[] = $breadcrumb_link;
		$i++;
	}
	$breadcrumbs_link = implode('<i class="fa fa-fw fa-angle-double-right"></i>', $breadcrumbs_link);
	return $breadcrumbs_link;
}

function full_name($first_name = null, $last_name = null) {
	$full_name = '';
	if (!empty($first_name)) {
		$full_name .= $first_name;
	}
	if (!empty($full_name)) {
		$full_name .= ' ';
	}
	if (!empty($last_name)) {
		$full_name .= $last_name;
	}
	return $full_name;
}

function next_work_day($date = null) {
	if (!$date) {
		$date = date('Y-m-d');
	}
	
	$next_work_day = date('Y-m-d', strtotime('+1 day', strtotime($date)));
	$next_work_day_no_year = date('m-d', strtotime('+1 day', strtotime($date)));
	
	$cz_holiday = cz_holiday();
	if (in_array($next_work_day_no_year, $cz_holiday)) {
		$next_work_day = next_work_day($next_work_day);
	}
	
	// nesmi byt svatek ani vikend - 0 je nedele, 6 je sobota
	$weekday = date("w", strtotime($next_work_day));

	if ($weekday == 0 || $weekday == 6) {
		$next_work_day = next_work_day($next_work_day);
	}
	
	return $next_work_day;
}

// test, jestli posilat sms notifikace na tel. cislo
function sendSMSNotification($phone) {
	// nechci posilat notifikace sobe
	$phone_blacklist = array('723238866', '7232388866');
	
	if (in_array($phone, $phone_blacklist)) {
		return false;
	}
	
	// nechci posilat sms na pevne linky
	$only_mobiles_pattern = '/^((?:\+|00)?420)?(?:2\d{8}|(?:31|32|35|37|38|39|41|47|46|48|49|51|53|54|55|59|56|57|58)\d{7})$/';
	if (preg_match($only_mobiles_pattern, $phone)) {
		return false;
	}
	
	return true;
}

function cz_holiday() {
	$holiday = array(
		0 => '01-01', // novy rok
		'05-01', // svatek prace
		'05-08', // den vitezstvi
		'07-05', // cyril a metodej
		'07-06', // jan hus
		'09-28', // den ceske statnosti
		'10-28', // vznik samostatneho CS statu
		'11-17', // den boje za svobodu
		'12-24', // stedry den
		'12-25', // 1. svatek vanocni
		'12-26' // 2. svatek vanoci
	);
	
	// pridam velikonocni pondeli
	$easter_sunday = easter_date(date('Y'));
	$easter_monday = strtotime('+1 day', $easter_sunday);
	$holiday[] = date('m-d', $easter_monday);
	return $holiday;
}

function latest_file($dir) {
	$latest_ctime = 0;
	$latest_filename = '';
	
	$d = dir($dir);
	while (false !== ($entry = $d->read())) {
		if ($entry != '.ftpquota') {
			$filepath = "{$dir}/{$entry}";
			// 	could do also other checks than just checking whether the entry is a file
			if (is_file($filepath) && filemtime($filepath) > $latest_ctime) {
				$latest_ctime = filemtime($filepath);
				$latest_filename = $entry;
			}
		}
	}
	return $latest_filename;
}

define('REDESIGN_PATH', 'redesign_2015/');
define('ROOT_CATEGORY_ID', 5);

define('HP_URI', '/');

define('FILES_DIR', 'files');
define('DOCUMENTS_DIR', FILES_DIR . DS . 'documents');
define('POHODA_EXPORT_DIR', DOCUMENTS_DIR . DS . 'pohoda_exports');

$host = 'localhost';
define('__DB_HOST__', $host);
//define('IMAGE_IP', '78.80.90.21');
define('IMAGE_IP', 'odstranit');

define('PDK_DIAL_DIR', 'files/pdk_ciselniky');

// kontrola, zda nezadame URI ktere ma byt presmerovano
App::import('Model', 'Redirect');
$this->Redirect = &new Redirect;
if ($r = $this->Redirect->check($_SERVER['REQUEST_URI'])) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " . $r['Redirect']['target_uri']);
	exit();
}

function utm_parameters_string($getParams) {
	$res = array();
	$utmTags = array('source', 'medium', 'campaign', 'content');
	foreach ($utmTags as $tag) {
		$tagName = 'utm_' . $tag;
		if (isset($getParams[$tagName])) {
			$res[] = $tagName . '=' . $getParams[$tagName];
		}
	}
	return implode('&', $res);
}

function sendMail($subject, $body, $email, $name = null, $isHtml = true, $senderEmail = null, $reply = true) {
	App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
	$mail = &new PHPMailer;
	
	if (!$senderEmail) {
		$senderEmail = CUST_MAIL;
	}
	
	// uvodni nastaveni
	$mail->CharSet = 'utf-8';
	$mail->Hostname = CUST_ROOT;
	$mail->Sender = $senderEmail;
	$mail->IsHTML($isHtml);
	
	// nastavim adresu, od koho se poslal email
	$mail->From     = $senderEmail;
	$mail->FromName = CUST_NAME;
	
	if ($reply) {
		$mail->AddReplyTo($senderEmail, CUST_NAME);
	}
	
	$mail->AddAddress($email, $name);
	
	$mail->Subject = $subject;
	$mail->Body = $body;

	return $mail->Send();
}

function getCache($fileName, $cacheLength) {
	if (file_exists($fileName)) {
		// cas, kdy byl modifikovan
		$mtime = filemtime($fileName);
		$nowtime = time();
	
		if ($cacheLength >= ($nowtime - $mtime)) {
			$res = file_get_contents($fileName);
			$res = unserialize($res);
			return $res;
		}
	}
}

function writeCache($fileName, $content) {
	return file_put_contents($fileName, serialize($content));
}
?>
