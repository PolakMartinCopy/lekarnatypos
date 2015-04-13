<div class="mainContentWrapper">
	<form action="/searches/parsequery" method="post">
		<input type="text" name="data[Search][q]" value="<?=$this->params['pass'][0]?>" />
		<input type="submit" value="hledej" />
	</form>
		<?
		if ( isset($xml['GSP'][0]['RES']) ){
			$results = array();
			$results = $xml['GSP'][0]['RES'][0];
		?>
		<p class="res">Zobrazuji výsledky <?=$results['ATTRIBUTES']['SN']?> až <?=$results['ATTRIBUTES']['EN']?> z <?=$results['M'][0]['VALUE']?> výsledků celkem.</p>
			<?
			foreach ( $results['R'] as $result ){
				// cislo vysledku $result['ATTRIBUTES']['N']
			?>
				<p class="title"><a href="<?=$result['U'][0]['VALUE']?>"><?=$result['T'][0]['VALUE']?></a></p>
				<?
				$result['S'][0]['VALUE'] = eregi_replace("<br>", "", $result['S'][0]['VALUE']);
				$result['S'][0]['VALUE'] = eregi_replace("<b>", "<strong>", $result['S'][0]['VALUE']);
				$result['S'][0]['VALUE'] = eregi_replace("</b>", "</strong>", $result['S'][0]['VALUE']);
				?>
				<p class="resultdesc"><?=$result['S'][0]['VALUE']?></p>
				<p class="url"><?=$result['U'][0]['VALUE']?> - <?=$result['HAS'][0]['C'][0]['ATTRIBUTES']['SZ']?></p>
			<?
			}
			$totalPages = ceil($results['M'][0]['VALUE'] / 10);
			$actualPage = $this->params['pass'][1] / 10;
			?>
				<p class="pages">Stránka s výsledky: 
			<?
			for ( $i = 0; $i < $totalPages; $i++ ){
				if ( $i != $actualPage ){
			?>
					&nbsp;<a href="/searches/index/<?=urlencode($this->params['pass'][0])?>/<?=( $i * 10 )?>"><?=( $i + 1 )?></a>&nbsp;
				<?
				}
				else{
				?>
					<strong><?=( $i + 1 )?></strong>
				<?
				}
			}
			?>
				</p>
		<?
		}
		else{
			if ( isset($this->params['pass'][0]) && isset($this->params['pass'][1]) ){
		?>
				<p class="res">Pro Váš dotaz nebyly nalezeny žádné výsledky.</p>
			<?
			} else {
			?>
				<p class="res">Zadejte Váš dotaz.</p>
		<?
			}
		}
		?>
	<p>Pro vyhledávání na tomto webu je použita technologie <a href="http://google.cz" target="_blank">Google</a>.<br />
	<img src="http://www.mte.cz/images/g.jpg" width="50px" height="50px" alt="Google logo" /></p>
</div>
