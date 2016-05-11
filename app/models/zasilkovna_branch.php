<?php
class ZasilkovnaBranch extends AppModel {
	var $name = 'ZasilkovnaBranch';

	var $actsAs = array('Containable');

	var $xmlFile = 'http://www.zasilkovna.cz/api/v3/8d343c98d33c0917/branch.xml';
//	var $xmlFile = 'http://localhost/files/zasilkovna_branches.xml';

}