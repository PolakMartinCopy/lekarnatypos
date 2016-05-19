<?
class Export extends AppModel{
	var $name = 'Export';
	
	var $useTable = false;
	
	function saveFeed($feedName, $feedUrl) {
		$content = download_url($feedUrl);
		return file_put_contents($feedName, $content);
	}
}
?>