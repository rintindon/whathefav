<?php
	// handle the action
	function replaceTags($text, $tag, $replacement){
		$newtext=$text;
		$newtext = str_replace($tag, $replacement, $newtext);
		return $newtext;
	}
	
	// prevent automatic usage
	if ( (trim($_COOKIE["u"]) =='' ) || ($_COOKIE["u"]!= base64_decode($_COOKIE["a"]) )){
		// no cookie or wrong cookie
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: http://www.google.com/");
		die();
	}

	switch($_REQUEST['req']){
		case 'postPollResults':
			// get the vote
			$resultList = $_REQUEST['resultList'];
			$combinationResult = $_REQUEST['combinationResult'];
			// sanitize pollId, because it contains the file name
			$pollId = str_replace('/', '', str_replace('\\', '', str_replace("'", "", escapeshellcmd($_REQUEST['pollId'])) ) );
			
			$file = '../options/' . $pollId . '/results.json';
			// use || for separating values and %% for separating votes
			$data = json_encode($resultList) . '||' . json_encode($combinationResult) . '%%';
			echo 'File '.$file;
			echo ' ';
			echo $data;
			file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
		break;
		case 'getPollResults':
			// sanitize pollId, because it contains the file name
			$pollId = str_replace('/', '', str_replace('\\', '', str_replace("'", "", escapeshellcmd($_REQUEST['pollId'])) ) );
			$file = '../options/' . $pollId . '/results.json';
			if (! (file_exists ($file) ) ){
				echo json_encode('no_results');
				die;
			}
			$data = file_get_contents($file, LOCK_SH);
			// extract every vote
			
			$votes = explode("%%", $data);
			// get amount of entries by counting the commas ,
			$sizeOfAr = substr_count(explode('||', $votes[0])[0], ',') + 1;
			// create the calculation array
			$calculationArray = []; 
			array_fill(0, $sizeOfAr, $calculationArray);
			// get only the results (first occurence) and sum them up
			$votesCount = 0;
			foreach ($votes as $vote) {
				$results = explode('||', $vote);
				$result = $results[0];
				// $result is a string, we need to convert it to an array
				$resultArray = json_decode($result);
				
				$i = 0;
				if ($resultArray != NULL){
					foreach ($resultArray as $singleVote){
						$calculationArray[$i] = $calculationArray[$i] + $singleVote;
						$i++;
					}
					$votesCount++;
				}
				
			}
			echo '{ "resultList" : '.json_encode($calculationArray).' , "votesCount" : '.$votesCount.' }';
		break;
		case 'createNewPoll':
			// we just create pollId, the rest is automatic :)
			require(dirname(__FILE__) .'/recaptcha/autoload.php');
			// pollId is automatically created
			$userEmail = $_REQUEST['e-mail'];
			$optionList = $_REQUEST['optionList'];
			$pollMode = $_REQUEST['pollMode'];
			$pollTitle = json_decode($_REQUEST['pollTitle'], true);
			$pollDescription = json_decode($_REQUEST['pollDescription'], true);
			$noBothMode = $_REQUEST['noBothMode'];
			$lang = $_REQUEST['lang'];
			// check if submission is human
			$recaptcha = new \ReCaptcha\ReCaptcha('6LdexhATAAAAAE-tUMVdbE-w0S59yaEUXRyEIiyn');
			$resp = $recaptcha->verify($_REQUEST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
			if (!($resp->isSuccess())) {
				// kein Human
				echo json_encode('wrong-user');
				die();
			}
			
			$pollId = substr(md5(md5($userEmail) . chr(rand(65,100)).uniqid()),0, 8);
			$directory = '../options/'.$pollId;
			// check if pollId alredy exists (almost impossible, but theoretically)
			while (is_dir($directory)){
				$pollId = substr(md5(md5($userEmail) . chr(rand(65,100)).uniqid()),0, 8);
				$directory = '../options/'.$pollId;
			}
			// create directory
			mkdir($directory);
			$file = $directory . '/pollConf.json';
			// prepare configuration file
			
			$conf["options"] = $optionList;
			$conf["pollMode"] = $pollMode;
			$conf["pollTitle"] = $pollTitle;
			$conf["pollDescription"] = $pollDescription;
			$conf["amountOfoptions"] = sizeof($optionList);
			$conf["noBothMode"] = $noBothMode;
			
			$data = json_encode($conf);
			file_put_contents($file, $data);
			
			$url = 'http://www.WhatTheFav.com/?'.$pollId;
			
			
			$emailContent = file_get_contents('mailContent.html');	
			$emailContent = replaceTags($emailContent, '<REPLACE-URL>', $url);
			$emailContent = replaceTags($emailContent, '<REPLACE-POLLID>', $pollId);
			$emailContent = replaceTags($emailContent, '<REPLACE-TITLE>', $pollTitle[$lang]);
			$emailContent = replaceTags($emailContent, '<REPLACE-TITLE>', $pollTitle[$lang]);
			
			$userEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);
			if (!(filter_var($userEmail, FILTER_VALIDATE_EMAIL))){
				echo json_encode('e-mail-incorrect');
				die();
			}
			$userEmail = $userEmail;
			$headers = 'From: WhatTheFav <noreply@whatthefav.com>' . "\r\n" .
						'X-Mailer: WhatTheFav'. "\r\n".
						"MIME-Version: 1.0\r\n".
						"Content-Type: text/html; charset=UTF8\r\n".
						'Bcc: valerioneri.de@gmail.com';
			mail($userEmail, 'WhatTheFav - '.($pollTitle[$lang]), $emailContent, $headers);
			echo json_encode('ok');
		break;
		case 'checkUrl':
			function URLIsValid($URL){
				if (preg_match("@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS", $URL)===FALSE){
					return false;
				}
				stream_context_set_default(
					array(
						'http' => array(
							'method' => 'HEAD'
						)
					)
				);
				$exists = true;
				$file_headers = @get_headers($URL, 1);
				$InvalidHeaders = array('404', '403', '500');
				foreach($InvalidHeaders as $HeaderVal){
					if(strstr($file_headers[0], $HeaderVal)!==FALSE){
						$exists = false;
						break;
					}
				}
				if ((strstr($file_headers['Content-Type'], 'image')===FALSE)){
					$exists = false;
				}
				return $exists;
			}
			$urlToCheck = $_REQUEST['url'];
			echo (URLIsValid($urlToCheck) ? 1 : 0);
		break;
	}
?>