<?php setcookie('u', md5(uniqid('', true)));// this is checked by the backend  ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, minimal-ui" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="white" />
		<title>What The Fav<?php require(dirname(__FILE__) .'/backend/getTitle.php'); ?></title>
		<meta property="og:title" data-lang="title" data-lang-type="content" content="What The Fav<?php require(dirname(__FILE__) .'/backend/getTitle.php'); ?>" />
		<meta property="og:description" data-lang="title" data-lang-type="description" content="<?php require(dirname(__FILE__) .'/backend/getDescription.php'); ?>" />
		<meta property="og:image" content="<?php $imageNum=1; require(dirname(__FILE__) .'/backend/getImage.php'); ?>" />
		<meta property="og:image" content="<?php $imageNum=2; require(dirname(__FILE__) .'/backend/getImage.php'); ?>" />
		<meta property="og:image" content="<?php $imageNum=3; require(dirname(__FILE__) .'/backend/getImage.php'); ?>" />
		<!-- CSS -->
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
		<link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
		<!-- Scripts-->
		<script src="http://www.google.com/jsapi"></script>
		<script src="js/jquery-2.1.1.min.js"></script>
		<script src="js/materialize.js"></script>
		<script src="js/lib_lang.js" data-lang-option="no-autostart"></script>
		<script src="js/lang_en.js"></script>
		<script src="js/lang_it.js"></script>
		<script src="js/lang_de.js"></script>
		<script src="js/wtfav.js"></script>
		<script src="js/xhrClient.js"></script>
		<script type="text/javascript" src="//static.addtoany.com/menu/page.js"></script>
	</head>
	<body>
		
		<!-- **************************************** -->
		<!-- ********** LANGUAGE SELECTION ********** -->
		<!-- **************************************** -->
		<div id="langContainer">
				<!--
				<div class="languageSelection right hide-on-small-only">
				</div>
				<div class="languageSelection right hide-on-med-and-up">
				</div>-->
			<div style="position:fixed;bottom:5px; right: 23px;margin-bottom:9px;">
				<div class="fixed-action-btn click-to-toggle" style="bottom:90px;">
					<a class="btn-floating btn yellow darken-3">
						<i class="material-icons">language</i>
					</a>
					<ul class="languageSelection" style="bottom:35px;">
					</ul>
				</div><br>
				<a class="green modal-trigger btn-floating" style="margin-bottom:1px;z-index:100;" id="socialPluginButton" href="#socialPlugins"><i class="material-icons">share</i></a><br>
				<button id="helpbutton" data-target="preSelectionPage" style="z-index:100;" class="modal-trigger btn-floating waves-light waves-effect blue"><i class="material-icons">info</i></button>
			</div>
		</div>

		<!-- ********************************** -->
		<!-- ********** LANDING PAGE ********** -->
		<!-- ********************************** -->
		<div id="landingPage" class="section">
			<div class="container">
				<div class="">
					<div class="middle">
						<br>
						<div class="row">
							<h1 class="header center orange-text" data-lang="title"></h1>
							<h4 class="flow-text" data-lang="welcome-message"></h4>
						</div>
						<div class="row center">
							<div class="col m12 s12 l6">
								<button class="btn waves-effect waves-light green" onclick="activateCreationPage()" style="width:100% !important" data-lang="register"></button>
							</div><br class="hide-on-large-only"><br class="hide-on-large-only">
							<div class="col m12 s12 l6">
								<button class=" btn waves-effect waves-light blue" onclick="window.location.href='?example'"  style="width:100% !important" data-lang="example"></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div id="wtfexplanation" class="modal">
				<div class="modal-content">
					<div class="flow-text" data-lang="wtf-explanation"></div>
				</div>
			</div>
		</div>
		<!-- ******************************************* -->
		<!-- ********** SOCIAL        PLUGINS ********** -->
		<!-- ******************************************* -->		

		<div class="modal bottom-sheet center" id="socialPlugins"  style="padding:20px;">
			<!-- AddToAny BEGIN -->
			<div class="a2a_kit a2a_kit_size_64 a2a_default_style center">
				<a class="a2a_button_facebook"></a>
				<a class="a2a_button_twitter"></a>
				<a class="a2a_button_google_plus"></a>
				<a class="a2a_button_linkedin"></a>
				<a class="a2a_button_xing"></a>
				<a class="a2a_button_outlook_com"></a>
				<a class="a2a_button_hacker_news"></a>
				<a class="a2a_button_google_bookmarks"></a>
				<a class="a2a_button_delicious"></a>
				<a class="a2a_button_whatsapp"></a>
				<a class="a2a_button_google_gmail"></a>
				<a class="a2a_button_email"></a>
				<a class="a2a_dd" href="https://www.addtoany.com/share"></a>
			</div>
			<!-- AddToAny END -->
		</div>
		<div id="fb-root" class="hidden"></div>
		
		<!-- ******************************************* -->
		<!-- ********** SECTION BEFORE VOTING ********** -->
		<!-- ******************************************* -->		
		<div id="preSelectionPage" class="modal">
			<div class="modal-content">
				<h1 class="header center orange-text" data-lang="title-description"></h1>
				<div class="row center">
					<h5 class="header col s12 light" id="message" data-lang="explanation"></h5>
				</div>
				<div class="modal-footer">
					<a href="#!" id="preSelectionCloseButton" class="modal-action modal-close waves-effect waves-green btn">Ok</a>
					<a href="#!" id="activatePollResults" style="padding-right:5px;" class="modal-close hidden left btn-flat" onclick="activatePollResults()" data-lang="poll-results"></a>
					<a href="#!" id="testPoll" style="padding-right:5px;" class="modal-close hidden left btn-flat" onclick="activateTestPoll()" data-lang="test-poll"></a>
					<a href="#!" style="padding-right:5px;" class="modal-close left btn-flat" onclick="window.location.href='/'" >Home</a>
					<a href="#disclaimerPage" style="padding-right:5px;" class="modal-close modal-trigger left btn-flat" target="_new" data-lang="disclaimer"></a>
				</div>
			</div>
		</div>
		
		<!-- **************************************** -->
		<!-- **********     DISCLAIMER     ********** -->
		<!-- **************************************** -->
		<div id="disclaimerPage" class="modal">
			<div class="modal-content">
				<h1 class="header center orange-text" data-lang="disclaimer"></h1>
				<div class="row flow-text">
					<h5 class="header col s12 light" id="message" data-lang="TOS"></h5>
				</div>
				<div class="modal-footer">
					<a href="#!" id="preSelectionCloseButton" class="modal-action modal-close waves-effect waves-green btn">Ok</a>
				</div>
			</div>
		</div>
				
		
		
		<!-- **************************************** -->
		<!-- ********** SECTION FOR VOTING ********** -->
		<!-- **************************************** -->
		<div id="votingSection" class="container valign-wrapper hidden">
			<div id="selectionPage" class="container valign hidden">
				<div id="optionContainer" class="row center">
					<div id="pictureContainer" class="col s12 m12 l12 center valign-wrapper">
						<div class="col s6 m6 l6 valign">
							<img width="100%" class="materialboxed" src="" id="option1" alt="Option 1" data-id="0" onload="imageLoaded(1, null)" />
						</div>
						<div class="col s6 m6 l6 valign">
							<img width="100%" class="materialboxed" src="" id="option2" alt="Option 2" data-id="1" onload="imageLoaded(1, null)" />
						</div>
					</div>
					<div id="textContainer" class="col s12 m12 l12 center valign-wrapper">
						<div class="col s6 m6 l6 valign align-center">
							<span class="flow-text center" id="option1" alt="Option 1" data-id="0" />
						</div>
						<div class="col s6 m6 l6 valign align-center">
							<span class="flow-text center" id="option2" alt="Option 2" data-id="1"  />
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="card hidden" id="buttonContainer">
			<div class="container middle">
				<div class="center hide-on-med-and-up">
					<button class="btn waves-effect waves-light green bCont voteButton" onclick="track('1')" data-lang="like-left-one" data-lang-type="title">&#x2191;</button>
					<button class="btn waves-effect waves-light blue iLikeBoth bCont voteButton" onclick="track('both')" data-lang="like-both" data-lang-type="title"><span data-lang="both"></span></button>
					<button class="btn waves-effect waves-light green bCont voteButton" onclick="track('2')" data-lang="like-right-one" data-lang-type="title">&#x2191;</button>
				</div>
				<div class="row center hide-on-small-only">
					<div class="col m4">
						<button class="btn-large waves-effect waves-light green voteButton" onclick="track('1')" data-lang="like-left-one" data-lang-type="title"><i class="material-icons left">thumb_up</i><span data-lang="left-one"></span></button>
					</div>
					<div class="col m4">
						<button class="btn-large waves-effect waves-light blue iLikeBoth voteButton" onclick="track('both')" data-lang="like-both" data-lang-type="title"><i class="material-icons left">thumb_up</i><span data-lang="both"></span></button>
					</div>
					<div class="col m4">
						<button class="btn-large waves-effect waves-light green voteButton" onclick="track('2')" data-lang="like-right-one" data-lang-type="title"><i class="material-icons left">thumb_up</i><span data-lang="right-one"></span></button>
					</div>
				</div>
				<div class="row center">
					<div id="howlong" class="orange" style="width:0%"><span class="black-text"><!-- 0% --></span></div>
				</div>
			</div>
		</div>
		<!--
		<br class="hide-on-small-only"><br class="hide-on-small-only">
		<br class="hide-on-med-and-up"><br class="hide-on-med-and-up"><br class="hide-on-med-and-up"><br class="hide-on-med-and-up"><br class="hide-on-med-and-up">
		<br class="hide-on-med-and-up"><br class="hide-on-med-and-up"><br class="hide-on-med-and-up"><br class="hide-on-med-and-up"><br class="hide-on-med-and-up"> -->
		
		<div id="pollResultsActivated" class="modal">
			<div class="modal-content">
				<h1 class="header center orange-text" data-lang="poll-results"></h1>
				<h2 class="header center orange-text"><span id="votesCount"></span> <span data-lang="poll-result-amount"></span></h2>
				<div class="modal-footer">
					<a href="#!" class="modal-action modal-close waves-effect waves-green btn">Ok</a>
				</div>
			</div>
		</div>
		<!-- ***************************************** -->
		<!-- ********** SECTION FOR REPORTS ********** -->
		<!-- ***************************************** -->
		<div id="voteReport" style="padding-top:5%;" class="row center hidden" >
			<div class="col s12 m6">
				<div class="card large">
					<div class="col s12 m12 valign" id="chart" style="width:100%; height:100%;"></div>
				</div>
			</div>
			<div class="col s12 m6">
				<div class="card large valign-wrapper">
					<img src="" width="auto" style="padding:5px !important;margin-left:auto;margin-right:auto;max-width:100%;max-height:100%;" width="auto" height="auto" class="hidden"  id="chartSelectedOption" />
					<div class="flow-text center valign hidden" style="width: auto !important; margin-right: auto; margin-left: auto;" id="chartSelectedOptionText"></div>
				</div>
			</div>
		</div>
		
		<!-- ****************************************** -->
		<!-- ********** SECTION FOR CREATION ********** -->
		<!-- ****************************************** -->
		<div id="creationPage" class="container hidden" >
			<div class="valign">
				<div class="row">
					<form class="col s12" id="creatorForm" onsubmit="if (!(document.getElementById('creationSave').classList.contains('disabled'))){prepareDataNewPollAndSend();};return false;" action="">
						<h2><span data-lang="create-new-poll"></span></h2>
						<div class="row card-panel">
							<label><span data-lang="input-languages"></span></label><br>
							<p>
								<input type="checkbox" id="creator.select.de"  class="creator-select" onchange="if (creatorSelectorChangeAllowed()){activatePollElement('pollTitle_de',( this.checked ? true  : false));activatePollElement('pollDescription_de',( this.checked ? true  : false));} else this.checked=true"/>
								<label for="creator.select.de">Deutsch</label>
							</p>
							<p>
								<input type="checkbox" id="creator.select.en" class="creator-select"  onchange="if (creatorSelectorChangeAllowed()){activatePollElement('pollTitle_en',( this.checked ? true  : false));activatePollElement('pollDescription_en',( this.checked ? true  : false));} else this.checked=true"/>
								<label for="creator.select.en">English</label>
							</p>
							<p>
								<input type="checkbox" id="creator.select.it" class="creator-select"  onchange="if (creatorSelectorChangeAllowed()){activatePollElement('pollTitle_it',( this.checked ? true  : false));activatePollElement('pollDescription_it',( this.checked ? true  : false));} else this.checked=true"/>
								<label for="creator.select.it">Italiano</label>
							</p>
						</div>
						
						<!-- TITLE -->
						<div class="row card-panel">
							<label><span data-lang="input-title-of-poll"></span></label><br>
							<div class="input-field col s12 m4 l4 creator-pollTitle" data-lang-spec="de" id="pollTitle_de_selector">
								<input id="pollTitle_de" type="text" class="validate" required>
								<label for="pollTitle_de"><span data-lang="title-of-poll"></span> (Deutsch)</label>
							</div>
							<div class="input-field col s12 m4 l4 creator-pollTitle" data-lang-spec="en" id="pollTitle_en_selector">
								<input id="pollTitle_en" type="text" class="validate" required>
								<label for="pollTitle_en"><span data-lang="title-of-poll"></span> (English)</label>
							</div>
							<div class="input-field col s12 m4 l4 creator-pollTitle" data-lang-spec="it" id="pollTitle_it_selector">
								<input id="pollTitle_it" type="text" class="validate" required>
								<label for="pollTitle_it"><span data-lang="title-of-poll"></span> (Italiano)</label>
							</div>
						</div>

						<!-- DESCRIPTION -->
						<div class="row card-panel">
							<label><span data-lang="input-description"></span></label><br>
							<div class="input-field col s12 m12 l4  creator-pollDescription" data-lang-spec="de" id="pollDescription_de_selector">
								<input id="pollDescription_de" type="text" class="validate" required>
								<label for="pollDescription_de"><span data-lang="description"></span> (Deutsch)</label>
							</div>
							<div class="input-field col s12 m12 l4 creator-pollDescription" data-lang-spec="en" id="pollDescription_en_selector">
								<input id="pollDescription_en" type="text" class="validate" required>
								<label for="pollDescription_en"><span data-lang="description"></span> (English)</label>
							</div>
							<div class="input-field col s12 m12 l4 creator-pollDescription" data-lang-spec="it" id="pollDescription_it_selector">
								<input id="pollDescription_it" type="text" class="validate" required>
								<label for="pollDescription_it"><span data-lang="description"></span> (Italiano)</label>
							</div>
						</div>
						
						<div class="row card-panel">
							<label><span data-lang="input-type-of-poll"></span></label><br>
							<div class="input-field col s12">
								<input name="pollModeCheck" checked="checked" type="radio" id="pollMode.images" onchange="if (this.checked) {removeClass('.textPoll');showClass('.imagePoll'); pollMode='image';} else {removeClass('.imagePoll');showClass('.textPoll'); pollMode='text';} " />
								<label for="pollMode.images"><span data-lang="type-of-poll-images"></span></label><br>
								<input name="pollModeCheck" type="radio" id="pollMode.textes" onchange="if (!this.checked) {removeClass('.textPoll');showClass('.imagePoll');pollMode='image';} else {removeClass('.imagePoll');showClass('.textPoll');pollMode='text';} "/>
								<label for="pollMode.textes"><span data-lang="type-of-poll-textes"></span></label>
							</div>
						</div>
						
						<div class="row card-panel" id="optionFields">
							<div class="row">
								<div class="col s12">
									<label><span data-lang="input-options"></span></label><br>
								</div>
							</div>
							<div class="row card-panel" id="optionField.1">
								<div class="input-field col s12">
									<input type="text" id="pollOptions.1" onchange="checkPollOptionUrl(this)" required/>
									<label for="pollOptions.1"><span data-lang="option"></span> 1</label>
								</div>
							</div>
							<div id="NewOptionInsertHere">
							</div>
							<!-- add button -->
							<div class="row">
								<!-- remove button -->
								<div class="col offset-s10 s2 right">
									<a class="waves-effect btn-floating waves-light red disabled" id="creator-remove-option-button" onclick="deleteInputField()"><i class="material-icons">remove</i></a>
									<a class="waves-effect btn-floating waves-light green" onclick="generateNewOptionInputField()"><i class="material-icons">add</i></a>
								</div>
							</div>
							
						</div>
						
						<div class="row card-panel">
							<label><span data-lang="input-both-button"></span></label><br>
							<div class="input-field col s12">
								<input name="noBothMode" type="radio" id="noBothMode.no" checked="" />
								<label for="noBothMode.no"><span data-lang="both-button-yes"></span></label><br>
								<input name="noBothMode" type="radio" id="noBothMode.yes" />
								<label for="noBothMode.yes"><span data-lang="both-button-no"></span></label>
							</div>
						</div>
						
						<div class="row card-panel">
							<label><span data-lang="input-email"></span></label><br>
							<div class="input-field col s12 m12 l12">
								<i class="material-icons prefix">mail</i>
								<input id="pollEmail" type="email" class="validate" required>
								<label for="pollEmail" data-error="!" data-success="Ok">E-Mail</label>
							</div>
						</div>
						<div class="row">
						<div class="g-recaptcha" data-sitekey="XXXXXXXXXXXXXXXXXXXXXXX" data-callback="activateSaveButton"></div>
						<script src='https://www.google.com/recaptcha/api.js'></script>
						<button class="waves-effect waves-green waves-light btn btn-large disabled" id="creationSave"><i class="material-icons left">save</i><span data-lang="save-poll"></span></button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- AFTER CREATION CONFIRMATION -->
		<div class="modal" id="afterCreationModal">
			<div class="modal-content">
				<div class="row center">
					<h5 class="header col s12 light" id="message" data-lang="creation-confirmation"></h5>
				</div>
				<div class="modal-footer">
					<a href="#!" id="preSelectionCloseButton" class="modal-action modal-close waves-effect waves-green btn" onclick="window.location.href='/'">Ok</a>
				</div>
			</div>
		</div>
		
	</body>
</html>
