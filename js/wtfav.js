// wtfav.js

// the amount of option should be at least 3
// the subdir will be set from the code passed by the ?location
var DEBUGMODE = false;

google.load("visualization", "1", {packages:["corechart"]});

var amountOfoptions = 0;
var noBothMode = false;
var pollId = window.location.search.substr(1, 999);
var subdir = 'options/'+pollId+'/'; //'options/';
var pointer1 = 1;
var pointer2 = 2;
var resultList = [];
var combinationResult = [];
var combinationAmount = 0;
var actualCombination = 1;
var loadedImages = 0;
var pollMode = "image";
var pollTitle = 'What The Fav';
var pollDescription = "";
var optSrc = [];
var chartData = '';

var getPollResultActivated = false;
var actualizePollOptionsTextAreaTimer = '';
var amountOfGeneratedOptions = 1;
var userEmail ='';
var grecaptcharesponse='';
var testPoll = false;

function debugLog(message){
	if (DEBUGMODE)
		console.log(message);
}

function generateNewOptionInputField(){
	// used by creator to insert new field
	amountOfGeneratedOptions++;
	var htmlCode = '<div class="row card-panel" id="optionField.'+amountOfGeneratedOptions+'"><div class="input-field col s12">'+
		'<input type="text" id="pollOptions.'+amountOfGeneratedOptions+'" onchange="checkPollOptionUrl(this)" required /><label for="pollOptions.'+amountOfGeneratedOptions+'">'+
		'<span data-lang="option"></span> '+amountOfGeneratedOptions+'</label></div><div class="col s1"></div></div>';
	var newElement = document.createElement('div');
	newElement.innerHTML = htmlCode;
	document.getElementById('NewOptionInsertHere').appendChild(newElement);
	document.getElementById('creator-remove-option-button').classList.remove('disabled');
	lib_lang.reload(checkAfterTranslation, 'nocreationUpdate');
}

function deleteInputField(){
	// used by creator to delete the last field
	if (amountOfGeneratedOptions>3){
		document.getElementById('optionField.'+amountOfGeneratedOptions).parentElement.removeChild(document.getElementById('optionField.'+amountOfGeneratedOptions));
		amountOfGeneratedOptions--;
		if (amountOfGeneratedOptions==3)
			document.getElementById('creator-remove-option-button').classList.add('disabled');
	}
}

function getPollConfiguration(callback){
	var pollConfiguration = subdir + 'pollConf.json';
	GET(pollConfiguration, function(res){
		amountOfoptions = res.amountOfoptions;
		pollTitle = res.pollTitle;
		noBothMode = (res.noBothMode==="true" ? true : false);
		// get the pollMode and throw away the parts that are not needed
		pollMode = res.pollMode;
		switch(pollMode){
			case 'image':
				$( "#textContainer" ).remove();
				removeClass('.textPoll');
			break;
			case 'text':
				$( "#pictureContainer" ).remove();
				removeClass('.imagePoll');
			break;
		}
		// get the specific messages, if any provided, and overwrite them to the language file, if not let the standard be
		// do the same with the title
		pollDescription = res.pollDescription;
		for (var i=0; i<lib_lang.languages.length; i++){
			if (typeof pollDescription[lib_lang.languages[i]] !== "undefined")
				eval(' lang_'+lib_lang.languages[i]+'["explanation"] = "'+pollDescription[lib_lang.languages[i]]+'"');
			if (typeof pollTitle[lib_lang.languages[i]] !== "undefined")
				eval(' lang_'+lib_lang.languages[i]+'["title-description"] = "'+pollTitle[lib_lang.languages[i]]+'"');
		}
		optSrc = res.options;
		debugLog(res);
		callback();
	} );
	
}

function languageSelection(selClass){
	var langCode = function(l){
		return '<li><a class="btn-floating yellow darken-3" onclick="setLng('+"'"+l+"'"+', checkAfterTranslation);">'+l+'</a></li>';
	}
	var languageSelections = document.querySelectorAll('.'+selClass);
	for (var i=0; i<languageSelections.length; i++){
		lib_lang.buildLanguageButtons(languageSelections[i], langCode);
	}
}

function calculateAmountOfCombinations(){
	var p1 = 1;
	var p2 = 2;
	var c = 1;
	while (p1<amountOfoptions){
		p2 = p2 + 1;
		if (p2>amountOfoptions){
			p1 = p1 + 1;
			p2 = p1 + 1;
			if (p1==amountOfoptions){
				return c;
			}
		}
		if ((p1<amountOfoptions) && (p2<=amountOfoptions)){
			c++;
		}
	}
}

function elaborateResult(callback){
	callback();
}

function setPercentage(){
	document.getElementById('howlong').style.width = Math.round(100*(actualCombination)/combinationAmount,0)+'%';
	//document.getElementById('howlong').innerHTML = Math.round(100*(actualCombination)/combinationAmount,0)+'%';
}

function removeClass(classes, visibility){
	var toRemove = document.querySelectorAll(classes);
	for (var i=0; i<toRemove.length; i++){
		if (visibility=='visibility')
			toRemove[i].style.visibility = 'hidden';
		else
			toRemove[i].classList.add('hidden');
			
	}
}

function showClass(classes){
	var toShow = document.querySelectorAll(classes);
	for (var i=0; i<toShow.length; i++){
		toShow[i].classList.remove('hidden');
	}
}

function activateSaveButton(result){
	document.getElementById('creationSave').classList.remove('disabled');
	// save the response code, in order to send it later to the server
	grecaptcharesponse = grecaptcha.getResponse();
}

function prepareDataNewPollAndSend(){
	optionList = [];
	var newOption='';
	for (var i=0; i<amountOfGeneratedOptions; i++){
		newOption = document.getElementById('pollOptions.'+(i+1)).value.trim();
		if (newOption !== '')
			optionList.push(newOption);
	}
	amountOfGeneratedOptions = optionList.length;
	userEmail = document.getElementById('pollEmail').value;
	noBothMode = document.getElementById('noBothMode.yes').checked;
	pollMode = (document.getElementById('pollMode.images').checked ? 'image' : 'text');
	
	pollTitle = {};	// this is important, otherwise, stringify doesnt work
	var titles = document.querySelectorAll('.creator-pollTitle>input[required]');
	for (var i=0; i<titles.length; i++){
		pollTitle[titles[i].parentElement.getAttribute('data-lang-spec')]=(titles[i].value.trim());
	}
	
	pollDescription = {};	// this is important, otherwise, stringify doesnt work
	var descriptions = document.querySelectorAll('.creator-pollDescription>input[required]');
	for (var i=0; i<descriptions.length; i++){
		pollDescription[descriptions[i].parentElement.getAttribute('data-lang-spec')]=(descriptions[i].value.trim());
	}
	
	var postData = { "optionList": optionList,
		"e-mail": userEmail,
		"pollMode": pollMode,
		"pollTitle": JSON.stringify(pollTitle),
		"pollDescription": JSON.stringify(pollDescription),
		"noBothMode" : noBothMode,
		"req":'createNewPoll',
		"g-recaptcha-response": grecaptcha.getResponse(),
		"lang" : lib_lang.lang
	};
	$.post('backend/wtfav.php', $.param(postData), function(r,err){
		res = JSON.parse(r);
		debugLog('Post new poll '+res);
		if (res=='ok')
			activateAfterCreationMessage();
		else{
			// reset the captcha
			grecaptcha.reset();
			// inform the user about the error
			Materialize.toast(lib_lang.gt(res));
		}
	});
	
}

function creatorSelectorChangeAllowed(){
	// prevent that all languages are deselected
	return (document.getElementById('creator.select.de').checked) || (document.getElementById('creator.select.en').checked)  || (document.getElementById('creator.select.it').checked);
}
	
function activatePollElement(el, activate){
	document.getElementById(el+'_selector').style.display = ( activate ? 'block' : 'none');
	if (activate)
		document.getElementById(el).setAttribute('required', 'required');
	else
		document.getElementById(el).removeAttribute('required');
}
	

function sendResult(){
	var postData = { "resultList": resultList,
		"combinationResult": combinationResult,
		"pollId": pollId,
		"req":'postPollResults'
	};
	// only if the user does not test the poll
	if (testPoll==false){
		$.post('backend/wtfav.php', $.param(postData), function(res,err){
			debugLog('Post result '+res);
		});
	}
}

function track(which){
	var d1 = document.getElementById('option1').getAttribute('data-id');
	var d2 = document.getElementById('option2').getAttribute('data-id');
	debugLog('d1 '+d1+'    d2 '+d2);
	switch(which){
		case '1':
			resultList[d1] = resultList[d1] + 2;
		break;
		case 'both':
			resultList[d1] = resultList[d1] + 1;
			resultList[d2] = resultList[d2] + 1;
		break;
		case '2':
			resultList[d2] = resultList[d2] + 2;
		break;
	}
	nextPointer(which);
}

function activatePollResults(){
	document.getElementById('optionContainer').classList.add('hidden');
	document.getElementById('votingSection').classList.add('hidden');
	document.getElementById('activatePollResults').classList.add('hidden');
	document.getElementById('testPoll').classList.add('hidden');
	getPollResultActivated = true;
	activateReportPage();
}

function activateTestPoll(){
	testPoll = true;
}

function activateCreationPage(){
	document.getElementById('optionContainer').classList.add('hidden');
	document.getElementById('votingSection').classList.add('hidden');
	document.getElementById('landingPage').classList.add('hidden');
	document.getElementById('voteReport').classList.add('hidden');
	document.getElementById('creationPage').classList.remove('hidden');
}

function activateAfterCreationMessage(){
	document.getElementById('creationPage').classList.add('hidden');
	$('#afterCreationModal').openModal();
}

function activateSocialPlugins(){
	// XING
	;(function (d, s) {
		var x = d.createElement(s),
		s = d.getElementsByTagName(s)[0];
		x.src = "https://www.xing-share.com/plugins/share.js";
		s.parentNode.insertBefore(x, s);
	})(document, "script");	
	
	// Facebook
	document.getElementById("social.facebook").setAttribute('data-href', window.location.href);
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/de_DE/sdk.js#xfbml=1&version=v2.5";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
}
	
function getPollResults(callback){
	GET('backend/wtfav.php?req=getPollResults&pollId='+pollId, function(res){
		if (res != 'no_results'){
			resultList = res.resultList;
			document.getElementById('votesCount').innerHTML = res.votesCount;
			callback();
		} else {
			// tell the user there are no results
			Materialize.toast(lib_lang.gt('no-poll-results'), 4000);
			// redirect to poll
			window.setTimeout(function(){window.location.href = '/?'+pollId}, 2000);
		}
	} );
}

function activateReportPage(){
	document.getElementById('voteReport').classList.remove('hidden');
	$( "#buttonContainer" ).remove();
	Materialize.toast(lib_lang.gt('do-mouse-over'), 4000)
	if (getPollResultActivated){
		getPollResults(drawChart);
		$('#pollResultsActivated').openModal();
	} else{
		drawChart();
	}
	
	function drawChart() {
		var dataArray = [
							[lib_lang.gt('option'), lib_lang.gt('votes'), { role: 'style' }]
						];
		// interpolate color according to value green 4CAF50 and orange FF9800 - 76|175|80 -- 255|152|0
		var maxValue = Math.max.apply(null, resultList);
		var minValue = Math.min.apply(null, resultList);
		for (var i=0; i<amountOfoptions; i++){
			var percentageOfValue = (resultList[i]-minValue) / (maxValue-minValue);
			var gradientColorRed = Math.round(255 + percentageOfValue * (76 - 255),0);
			var gradientColorGreen = Math.round(152 + percentageOfValue * (175 - 152), 0);
			var gradientColorBlue = Math.round(0 + percentageOfValue * (80), 0);
			dataArray.push([ optSrc[i], resultList[i], "color:rgb("+gradientColorRed+", "+gradientColorGreen+", "+gradientColorBlue+")" ]);
			debugLog("color:rgb("+gradientColorRed+", "+gradientColorGreen+", "+gradientColorBlue+")");
		};
		
		debugLog(dataArray);
		chartData = google.visualization.arrayToDataTable(dataArray);
		chartData.sort(1);
		var options = {
			legend: { 
				position : "none" 
			},
			chart: {
				height: '100%',
				width: '100%',
				chartArea: {
					width: '100%',
					height: '100%'
				}
			}
		};
		var chart = new google.visualization.BarChart(document.getElementById('chart'));
		chart.draw(chartData,options);
		// add listener only for picture, so that we can highlight the option
		// onmouseover
		
		google.visualization.events.addListener(chart, 'onmouseover', showSelectedOption);
		google.visualization.events.addListener(chart, 'click', showSelectedOption);
		
		
		function showSelectedOption(obj){
			try{
				if (typeof obj.row == "undefined")
					return;
				if (pollMode=="image"){
					document.getElementById('chartSelectedOption').src = chartData.getValue(obj.row, 0);
					document.getElementById('chartSelectedOption').classList.remove('hidden');
					$('body').scrollTo('#chartSelectedOption');
				} else {
					document.getElementById('chartSelectedOptionText').innerHTML = chartData.getValue(obj.row, 0);
					document.getElementById('chartSelectedOptionText').classList.remove('hidden');
					$('body').animate({ scrollTop: $('#chartSelectedOptionText').offset().top }, 500);
				}
			} catch(e){
			}
		}
		
	}
}
function checkPollOptionUrl(t){
	that=t;
	if (pollMode=='image'){ 
		checkUrlIfValid(that, 
			function(result){ 
				if(result==false){
					that.setCustomValidity(lib_lang.gt('invalid-url'));
					that.classList.add('invalid');
				} else {
					that.setCustomValidity('');
					that.classList.remove('invalid');
				} 
			}
		); 
	}
	
}
function checkUrlIfValid(element, callback){
	var postData = { "url": element.value,
		"req": 'checkUrl'
	};
	return (
	$.post('backend/wtfav.php', $.param(postData), function(res,err){
		debugLog('Post new poll '+res);
		callback((res==1 ? true : false));
		return (res==1 ? true : false);
	}) );
}

function nextPointer(w){
	combinationResult[pointer1+''+pointer2] = w;
	pointer2 = pointer2 + 1;
	if (pointer2>amountOfoptions){
		// the voting is over
		pointer1 = pointer1 + 1;
		pointer2 = pointer1 + 1;
		if (pointer1==amountOfoptions){
			// show the final thanks, send the results to the server and activate the report page
			document.getElementById('optionContainer').classList.add('hidden');
			document.getElementById('votingSection').classList.add('hidden');
			document.getElementById('activatePollResults').classList.add('hidden');
			document.getElementById('testPoll').classList.add('hidden');
			$('#preSelectionPage').openModal();
			setButtons(false, '.voteButton');
			document.getElementById('message').innerHTML = lib_lang.gt('thank-you-for-voting');
			document.getElementById('message').setAttribute('data-lang','thank-you-for-voting');
			document.getElementById('howlong').classList.remove('orange');
			document.getElementById('howlong').classList.add('green');
			setPercentage();
			elaborateResult( sendResult );
			activateReportPage();
			return;
		}
	}
	if ((pointer1<amountOfoptions) && (pointer2<=amountOfoptions)){
		// get the next combination
		setPercentage();
		actualCombination++;
		// lock the buttons, and unlock automatically after loading with onload on the image
		if (document.getElementById('option1').getAttribute('data-id') != (pointer1 -1) ){
			debugLog('load one');
			imageLoaded(-1, function(){
				setOption(1, pointer1  -1);
				document.getElementById('option1').setAttribute('data-id', pointer1 -1);
			} );
		}
		if (document.getElementById('option2').getAttribute('data-id') != (pointer2 -1) ){
			debugLog('load two');
			imageLoaded(-1, function(){
				setOption(2, pointer2  -1);
				document.getElementById('option2').setAttribute('data-id', pointer2 -1);
			} );
		}
	}
}

function setButtons(active, selector){
	if (typeof selector == "undefined")
		selector = '';
	var buttonList = document.querySelectorAll('button'+selector);
		for (var i=0; i<buttonList.length; i++) {
			if (active)
				buttonList[i].removeAttribute('disabled'); 
			else
				buttonList[i].setAttribute('disabled', 'disabled'); 
		}
	
	var buttonList = document.querySelectorAll('a'+selector);
		for (var i=0; i<buttonList.length; i++) {
			if (active)
				buttonList[i].removeAttribute('disabled'); 
			else
				buttonList[i].setAttribute('disabled', 'disabled'); 
		}
}

function imageLoaded(val, callback){
	debugLog(loadedImages);
	if (val==1)
		loadedImages++;
	if (val==-1)
		loadedImages--;
	if (loadedImages<2){
		// block all buttons, only if images are shown
		if (pollMode == "image")
			setButtons(false);
	}
	if (loadedImages==2){
		// both images have loaded, release all buttons, only if images are showm
		if (pollMode == "image")
			setButtons(true);
	}
	if (typeof callback == 'function')
		callback();
}

function checkAfterTranslation(opt){
	// the buttons for the creation page
	if (opt!=='nocreationUpdate'){
		// set the language buttons accordingly
		var creatorSelect = document.querySelectorAll('.creator-select');
		for (var i = 0; i<creatorSelect.length; i++) 
			creatorSelect[i].checked = false;
		document.getElementById('creator.select.'+lib_lang.lang).checked = true;
	}

	// set the title according to the chosen languages
	var creatorPollTitle = document.querySelectorAll('.creator-pollTitle');
	for (var i = 0; i<creatorPollTitle.length; i++){
		creatorPollTitle[i].style.display = ( document.getElementById('creator.select.'+creatorPollTitle[i].getAttribute('data-lang-spec')).checked ? 'block' : 'none');
		if (document.getElementById('creator.select.'+creatorPollTitle[i].getAttribute('data-lang-spec')).checked)
			document.getElementById('pollTitle_'+creatorPollTitle[i].getAttribute('data-lang-spec')).setAttribute('required', 'required');
		else
			document.getElementById('pollTitle_'+creatorPollTitle[i].getAttribute('data-lang-spec')).removeAttribute('required');
	}
	
	var creatorPollDescription = document.querySelectorAll('.creator-pollDescription');
	for (var i = 0; i<creatorPollDescription.length; i++){
		creatorPollDescription[i].style.display = ( document.getElementById('creator.select.'+creatorPollDescription[i].getAttribute('data-lang-spec')).checked ? 'block' : 'none');
		if (document.getElementById('creator.select.'+creatorPollDescription[i].getAttribute('data-lang-spec')).checked)
			document.getElementById('pollDescription_'+creatorPollDescription[i].getAttribute('data-lang-spec')).setAttribute('required', 'required');
		else
			document.getElementById('pollDescription_'+creatorPollDescription[i].getAttribute('data-lang-spec')).removeAttribute('required');
	}
	
	
	// remove the middle button
	if (noBothMode==true){
		removeClass('.iLikeBoth', 'visibility');
	}
	
	// check which type of poll is set in the creator
	if (document.getElementById('pollMode.images').checked){
		removeClass('.textPoll');
		showClass('.imagePoll');
	} else {
		removeClass('.imagePoll');
		showClass('.textPoll')
	}
	
	// change the title according to the language
	document.title = 'What The Fav' + ( typeof pollTitle[lib_lang.lang] == 'undefined' ? '' : ' - ' + pollTitle[lib_lang.lang] );
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
} 

function setOption(num, position){
	// set the option, according if image or text
	switch(pollMode){
		case 'image':
			document.getElementById('option'+num).src = optSrc[position];
			break;
		case 'text':
			document.getElementById('option'+num).innerHTML = optSrc[position];
			break;
	}
	
}

window.addEventListener('load', function(){
	// build the page
	if (subdir.length <= 9){
		// ACTIVATE LANDING PAGE
		document.getElementById('landingPage').classList.remove('hidden');
		document.getElementById('selectionPage').classList.add('hidden');
		document.getElementById('buttonContainer').classList.add('hidden');
		lib_lang.reload(checkAfterTranslation);
	} else {
		// ACTIVATE INTRODUCTION AND COMPARISON PAGE
		getPollConfiguration( function(){
			document.getElementById('landingPage').classList.add('hidden');
			document.getElementById('selectionPage').classList.remove('hidden');
			document.getElementById('votingSection').classList.remove('hidden');
			document.getElementById('buttonContainer').classList.remove('hidden');
			document.getElementById('activatePollResults').classList.remove('hidden');
			document.getElementById('testPoll').classList.remove('hidden');
			for (var i=0;i<amountOfoptions;i++){
				resultList[resultList.length] = 0;
			}
			setOption(1, 1 -1);
			setOption(2, 2 -1);
			
			combinationAmount = calculateAmountOfCombinations();
			lib_lang.reload(checkAfterTranslation);
			$('#preSelectionPage').openModal();
		} );
	}
	languageSelection('languageSelection');
	$('.modal-trigger').leanModal();
	$('select').material_select();
	generateNewOptionInputField();
	generateNewOptionInputField();
	removeClass('.textPoll');
	showClass('.imagePoll');
	document.getElementById('creator-remove-option-button').classList.add('disabled');
	sessionCookie = getCookie('u');
	document.cookie = 'a='+window.btoa(sessionCookie);
}, false);
