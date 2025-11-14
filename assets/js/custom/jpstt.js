function initSpeechRecognitation()
{
	const searchForm = document.querySelector("#search-form");
	const searchFormInput = searchForm.querySelector("input"); // <=> document.querySelector("#search-form input");

	// The speech recognition interface lives on the browser’s window object
	const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition; // if none exists -> undefined


	// setTimeout(function(){ alert("Hello"); }, 3000);
	if(SpeechRecognition) {
	  console.log("Your Browser supports speech Recognition");
	  
	  const recognition = new SpeechRecognition();
	  recognition.continuous = true;
	  // recognition.lang = "en-US";

	  searchForm.insertAdjacentHTML("beforeend", '<button type="button"><i class="fas fa-microphone"></i></button>');
	  searchFormInput.style.paddingRight = "50px";

	  const micBtn = searchForm.querySelector("button");
	  const micIcon = micBtn.firstElementChild;

	  micBtn.addEventListener("click", micBtnClick);
	  function micBtnClick() {
		if(micIcon.classList.contains("fa-microphone")) { // Start Voice Recognition
		  recognition.start(); // First time you have to allow access to mic!
		}
		else {recognition.stop(); }
	  }

	  recognition.addEventListener("start", startSpeechRecognition); // <=> recognition.onstart = function() {...}
	  function startSpeechRecognition() {
		micIcon.classList.remove("fa-microphone");
		micIcon.classList.add("fa-microphone-slash");
		searchFormInput.focus();
		console.log("Voice activated, SPEAK");
	  }

	  recognition.addEventListener("end", endSpeechRecognition); // <=> recognition.onend = function() {...}

	  function endSpeechRecognition() {
		micIcon.classList.remove("fa-microphone-slash");
		micIcon.classList.add("fa-microphone");
		searchFormInput.focus();
		console.log("Speech recognition service disconnected");
	  }

	  recognition.addEventListener("result", resultOfSpeechRecognition); // <=> recognition.onresult = function(event) {...} - Fires when you stop talking
	  function resultOfSpeechRecognition(event) {
		const current = event.resultIndex;
		const transcript = event.results[current][0].transcript;
		
		if(transcript.toLowerCase().trim()==="stop recording") {recognition.stop();}
		else if(!searchFormInput.value) {
			searchFormInput.value = transcript;
			$('.dataTables_filter .form-control-sm').val($.trim(transcript));
			$('.dataTables_filter .form-control-sm').trigger(jQuery.Event('keyup', { keycode: 39 }));
			recognition.stop();
		}
		else {
		  if(transcript.toLowerCase().trim()==="go") {searchForm.submit(); }
		  else if(transcript.toLowerCase().trim()==="reset input") {
			searchFormInput.value = "";
			$('.dataTables_filter .form-control-sm').val($.trim(transcript));
			$('.dataTables_filter .form-control-sm').trigger(jQuery.Event('keyup', { keycode: 39 }));
			recognition.stop();
		  }
		  else {
			searchFormInput.value = transcript;
			$('.dataTables_filter .form-control-sm').val($.trim(transcript));
			$('.dataTables_filter .form-control-sm').trigger(jQuery.Event('keyup', { keycode: 39 }));
			recognition.stop();
		  }
		}
	  }
	  
	  
	}
	else {
	  console.log("Your Browser does not support speech Recognition");
	  info.textContent = "Your Browser does not support Speech Recognition";
	}
}

function initSpeechRecognitationMenu()
{
	// The speech recognition interface lives on the browser’s window object
	const SpeechRecognitionMenu = window.SpeechRecognition || window.webkitSpeechRecognition; // if none exists -> undefined


	// setTimeout(function(){ alert("Hello"); }, 3000);
	if(SpeechRecognitionMenu) 
	{
		console.log("Your Browser supports speech Recognition");

		const recognitionMenu = new SpeechRecognitionMenu();
		recognitionMenu.continuous = true;
		
		const menuMic = document.querySelector(".menuMic");
		const menuMicIcon = menuMic.firstElementChild;

		menuMic.addEventListener("click", menuMicClick);
		function menuMicClick() {if(menuMicIcon.classList.contains("fa-microphone")) { recognitionMenu.start();}else {recognitionMenu.stop(); }}

		recognitionMenu.addEventListener("start", startSpeechRecognitionMenu); // <=> recognition.onstart = function() {...}
		function startSpeechRecognitionMenu() {
			menuMicIcon.classList.remove("fa-microphone");
			menuMicIcon.classList.add("fa-microphone-slash");
		}

		recognitionMenu.addEventListener("end", endSpeechRecognitionMenu);

		function endSpeechRecognitionMenu() {
			menuMicIcon.classList.remove("fa-microphone-slash");
			menuMicIcon.classList.add("fa-microphone");
		}

		recognitionMenu.addEventListener("result", resultOfSpeechRecognitionMenu);
		var menuList = {
							'terms' : 'terms','department' : 'hr/departments','employee' : 'hr/employees',
							'attendance' : 'hr/attendance','supplier' : 'parties/supplier','purchase order' : 'purchaseOrder',
							'store' : 'store','material issue' : 'jobMaterialDispatch','consumable' : 'items/consumable',
							'rm' : 'items/pitems','stock transfer' : 'store/items','grn' : 'grn',
							'vendor' : 'parties/vendor','job card' : 'jobcard','process' : 'process',
							'production approval' : 'processApproval','material request' : 'materialRequest','vendor jobwork' : 'jobWork',
							'machine' : 'machines','inward qc' : 'grn/materialInspection','gaj' : 'gauges',
							'instrument' : 'instrument','in challan' : 'inChallan','out challan' : 'outChallan',
							'delivery challan' : 'deliveryChallan','sales order' : 'salesOrder','sales invoice' : 'salesInvoice',
							'fg' : 'products', 'order karo' : 'salesOrder/addOrder'
						};
		function resultOfSpeechRecognitionMenu(event) {
			const current = event.resultIndex;
			const transcriptMenu = event.results[current][0].transcript;

			
			$.each(menuList, function (key, pageUrl) {if(transcriptMenu.toLowerCase().trim() == key){location.href= base_url + pageUrl;}});
			recognitionMenu.stop();
		}
	}
	else {console.log("Your Browser does not support speech Recognition");}
}