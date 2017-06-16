<?php

	$request = str_replace('?','',$_SERVER['QUERY_STRING']);
	// open poll configuration and get Title

	$forceLang = 'en';
	
	$pollId = $request;
	
	$file = dirname(__FILE__).'/../options/' . $pollId . '/pollConf.json';
	if ( (file_exists ($file) ) ){
		
		$data = file_get_contents($file, LOCK_SH);
		// extract every vote
		$pollConf = json_decode($data, true);
		$lang = array();
		if (trim($_SERVER["HTTP_ACCEPT_LANGUAGE"])!=''){
			$langs = explode(',',$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			$lang = $langs[0];
		} else{
			$lang = $forceLang;
		}
		try{
			echo ' - '.strip_tags($pollConf['pollTitle'][$lang]);
		} catch(Expection $e){
			
		}
	}
?>