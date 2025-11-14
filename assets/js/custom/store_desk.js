$(document).ready(function(){
	var prcListPageLimit = 15;
    setTimeout(function(){ loadDesk(); }, 50);
    
	$(document).on('click','.stageFilter',function(){
        var postdata = $(this).data('postdata') || {};
		var np = parseFloat($('#next_page').val()) || 0;
		postdata.start = 0;
		postdata.length = parseFloat(prcListPageLimit);
		postdata.page = 0;
		
		loadHtmlData({'fnget':'getPRCList','rescls':'prcList','postdata':postdata});
	});
	
	$('.quicksearch').keyup(delay(function (e) {
		e.preventDefault();
		$('#next_page').val('0');
		var postdata = $('.stageFilter.active').data('postdata') || {};
		delete postdata.page;delete postdata.start;delete postdata.length;
		postdata.limit = parseFloat(prcListPageLimit);
		postdata.skey = $(this).val();
		loadHtmlData({'fnget':'getPRCList','rescls':'prcList','postdata':postdata});
	}));
	
	const scrollEle = $('#sopBoard .simplebar-content-wrapper');
	var ScrollDebounce = true;
	$(scrollEle).scroll(function() {
		if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 10)) {
			if(ScrollDebounce){
				ScrollDebounce = false;
				var postdata = $('.stageFilter.active').data('postdata') || {};
    			var np = parseFloat($('#next_page').val()) || 0;
    			postdata.start = np * parseFloat(prcListPageLimit);
    			postdata.length = prcListPageLimit;
    			postdata.page = np;
    			loadHtmlData({'fnget':'getPRCList','rescls':'prcList','postdata':postdata,'scroll_type':1});
				setTimeout(function () { ScrollDebounce = true; }, 500);		
			}
		}
	});
});

/***** GET DYNAMIC DATA *****/
function loadHtmlData(data){
	
	var postData = data.postdata || {};
	var fnget = data.fnget || "";
	var controllerName = data.controller || controller;
	var rescls = data.rescls || "dynamicData";
	var scrollType = data.scroll_type || "";
	
	$.ajax({
		url: base_url + controllerName + '/' + fnget,
		data:postData,
		type: "POST",
		dataType:"json",
		global:false,
	}).done(function(res){
		$("#next_page").val(res.next_page);
		if(!scrollType){$("."+rescls).html(res.prcList);}else{$("."+rescls).append(res.prcList);}
		loading = true;
	});
}

function delay(callback, ms=500) {
	var timer = 0;
	return function() {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () { callback.apply(context, args); }, ms || 0);
	};
}

function loadDesk(){
	$(".stageFilter.active").trigger("click");
}