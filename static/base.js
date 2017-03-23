function loadingFadeIn(){
	$('.loading-modal').fadeIn("fast");
}
function loadingFadeOut() {
	$('.loading-modal').fadeOut("fast");
}
function getNowFormatDate() {
	var date = new Date();
	var seperator1 = "-";
	var seperator2 = ":";
	var month = date.getMonth() + 1;
	var strDate = date.getDate();
	if (month >= 1 && month <= 9) {
		month = "0" + month;
	}
	if (strDate >= 0 && strDate <= 9) {
		strDate = "0" + strDate;
	}
	var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
		+ " " + date.getHours() + seperator2 + date.getMinutes()
		+ seperator2 + date.getSeconds();
	return currentdate;
}
function loadMessageCenter(url, sendData) {
	$.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		data: sendData,
		cache: false,
        global: false,
		contentType: false,
		processData: false,
		beforeSend: function() {
			// 旋转刷新按钮图标
			$('#message-dropdown').find('.update-info > button i').addClass('fa-spin');
		},
		complete: function() {
			// 停止旋转刷新按钮图标
			$('#message-dropdown').find('.update-info > button i').removeClass('fa-spin');
		},
		success: function(json) {
			if (typeof json.errorCode == 'undefined' || json.errorCode != 0) {
				return false;
			}
			var data = json.data;
			var keys = new Array;
			var html = '<table class="table table-bordered table-hover dataTable no-margin" role="grid">';
			// head
			html += '<thead><tr>';
			$.each(data.title, function (k, v) {
				keys.push(k);
				html += '<th>' + v + '</th>';
			});
			html += '<th style="min-width:130px;">操作</th></tr></thead>';

			// body
			html += '<tbody>';
			$.each(data.list, function(k, v) {
				html += '<tr>';
				for (f in keys) {
					html += '<td>' + v[keys[f]] + '</td>';
				}
				if (typeof v.uri != 'undefined') {
					html += '<td><a onclick="javascript:window.location.href=\'' + v.uri + '\'">查看</a></td>';
				}
				html += '</tr>';
			});
			html += '</tbody>';
			html += '</table>';

			// pagination
			if (data.total > 1) {
				html += '<div class="box-tools pull-right margin"><ul class="pagination pagination-sm inline" data-action="' + url + '" data-page="' + sendData['page'] + '">';
				if (sendData['page'] > 1) {
					html += '<li><a href="javascript:;" data-page="1">«</a></li>';
					if (sendData['page'] > 2) {
						html += '<li><a href="javascript:;" data-page="' + (parseInt(sendData['page']) - 2) + '">' + (parseInt(sendData['page']) - 2) + '</a></li>';
					}
					html += '<li><a href="javascript:;" data-page="' + (parseInt(sendData['page']) - 1) + '">' + (parseInt(sendData['page']) - 1) + '</a></li>';
				}
				html += '<li class="active"><a href="javascript:;" data-page="' + sendData['page'] + '">' + sendData['page'] + '</a></li>';
				if (sendData['page'] < data.total) {
					html += '<li><a href="javascript:;" data-page="' + (parseInt(sendData['page']) + 1) + '">' + (parseInt(sendData['page']) + 1) + '</a></li>';
					if (sendData['page'] + 1 < data.total) {
						html += '<li><a href="javascript:;" data-page="' + (parseInt(sendData['page']) + 2) + '">' + (parseInt(sendData['page']) + 2) + '</a></li>';
					}
					html += '<li><a href="javascript:;" data-page="' + data.total + '">»</a></li>';
				}
				html += '</ul></div>';
			}

			// message number
			var messageButton = $('#button-message');
			var messageDropdown = $('#message-dropdown');
			messageButton.find("span").html(data.statistics.amount);
			messageDropdown.find(".btn-group-justified .btn.violation span").html("(" + data.statistics.violations + ")");
			messageDropdown.find(".btn-group-justified .btn.fault span").html("(" + data.statistics.faults + ")");
			messageDropdown.find(".btn-group-justified .btn.other span").html("(" + data.statistics.feedbacks + ")");

			$(".message-list>div").html(html);
			$(".update-info span").html(getNowFormatDate());
		},
		error: function(xhr, ajaxOptions, thrownError) {
			// 网络异常
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
	
}
(function($){
	var seed=new Array();
	
	window.View={
		init:function(){

		},
		//记录定时器
		addSeed:function(val){
			seed.push(val);
		},
		//清除定时器
		clearSeed:function(){
			for(var i=0;i<seed.length;i++){
				window.clearInterval(seed[i]);
			}
		}
	};

	$( document ).ajaxStart(function() {
		loadingFadeIn();
	});

	$( document ).ajaxSuccess(function() {
		loadingFadeOut();
	});

	$('html body').on('click','a',function(event){
		if($(this).attr('target')=='_blank' || $(this).attr('href').substr(0,10)=='javascript'){
			return;
		}
		event.preventDefault();
		var state={
			url:$(this).attr('href')
		};
		if($(this).attr('href')=='#'){
			return;
		}
		View.clearSeed();
		history.pushState(state,null,$(this).attr('href'));
		$.get($(this).attr('href'),function(res){
			if(res!=null && res!=undefined){
				$('#content-wrapper').html(res);
			}else{
				$('#content-wrapper').html('');
			}
		});
	});
	
	$('html body').on('click','button[type="submit"],input[type="submit"]',function(event){
		var form=$(this).parents('form');
		var query=form.serialize();
		var method=form.attr('method');
		var state={
			url:form.attr('action')
		};
		history.pushState(state,null,form.attr('action'));
		if(method=='get'){
			$.get(form.attr('action'),query,function(res){
				$('#content-wrapper').html(res);
			});
		}else{
			var oData = new FormData(form[0]);
			var oReq = new XMLHttpRequest();
			var url=form.attr('action');
			oReq.open("POST", url, true);
			oReq.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			oReq.send(oData);
			oReq.onload = function(oEvent) {
				$('#content-wrapper').html(oReq.responseText);
			};
//			$.post(form.attr('action'),query,function(res){
//				$('#content-wrapper').html(res);
//			});
		}
		
		return false;
	});
	
	
	$('html body').on('click','button.link',function(event){
		var state={
			url:$(this).attr('data-url')
		};
		history.pushState(state,null,$(this).attr('data-url'));
		$.get($(this).attr('data-url'),function(res){
			$('#content-wrapper').html(res);
		});
	});

	
	window.addEventListener("popstate", function(e) {
	    var url = e.state.url;
	    console.log(e.state);
		var state={
			url:url
		};
		history.pushState(state,null,url);
		$.get(url,function(res){
			$('#content-wrapper').html(res);
		});
	    
	});
	
	toastr.options = {
	  "closeButton": true,
	  "debug": false,
	  "positionClass": "toast-top-center",
	  "onclick": null,
	  "showDuration": "300",
	  "hideDuration": "1000",
	  "timeOut": "5000",
	  "extendedTimeOut": "1000",
	  "showEasing": "swing",
	  "hideEasing": "linear",
	  "showMethod": "fadeIn",
	  "hideMethod": "fadeOut"
	}

    var messageDropdown = $('#message-dropdown');

    messageDropdown.on('click', '>div, >span', function(e){
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
    }).on('click', '.btn-group .btn', function(e) { //消息中心切换标签
        $(this).addClass('active').siblings().removeClass('active');
		var page = 1;
		var url = $(this).data("action");
		loadMessageCenter(url+"&page="+page, {page:page});
    }).on('click', '.update-info > button', function() { //消息中心手动刷新
        var me = $(this);
        // $(this).find('i').addClass('fa-spin');
        // setTimeout(function() {
        //     me.find('i').removeClass('fa-spin');
        // },2000);
		messageDropdown.find(".btn-group-justified .btn.active").trigger("click");
    }).on('click', '.pagination a', function() { //消息中心分页
        var me = $(this);
		var page = me.data("page");
		var url = me.parents("ul").data("action");
		loadMessageCenter(url+"&page="+page, {page:page});
    });

	// 立即触发 更新
	messageDropdown.find('.update-info > button').trigger("click");
})(jQuery);