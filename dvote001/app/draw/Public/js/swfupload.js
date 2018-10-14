function remove_this(obj){
		$('#'+obj).remove();
}
function clean_thumb(imgid){
	$('#aidimg_'+imgid).remove();
	$('#fid_val_'+imgid).remove();
}

//图片上传  aid为图片上传标识ID
/*****
 * @param sting  id    标识打开的框架
 * @param string inputid 保存上传图片的div中ID的一部分
 * @param string title 弹出框的名字
 * @param string aid   上传图片的标识ID
 * @param string url   请求的url
 * @author orchid
 *****/
function swfupload(id,inputid,title,aid,url,isreplace,$file_url)
{
	art.dialog.open(url, {
				id: id,
				title: title,
				lock: 'true',
				window: 'top',
				width: 600,
				height: 455,
				ok: function(){
					var iframeWin = this.iframe.contentWindow;
		    		var topWin = art.dialog.top;
					yesdo.call(this,iframeWin, topWin,id,inputid,aid,isreplace);
					},
				cancel: true
				}
		);
}

function yesdo(iframeWin, topWin,id,inputid,aid,isreplace){
		datadiv = iframeWin.$('#myuploadform > div');
		var num = datadiv.length;
		if(num){
			var imgdata = '', datas = '', src, status, aids, namedata,filedata;
            datadiv.each(function() {
            	filedata= $(this).find('#filedata').val();
                status 	= $(this).find('#status').val();
                aids 	= $(this).find('#'+inputid).val();
                namedata= $(this).find('#namedata').val();
              
                if(isreplace==1){
                	datas ='<input type="hidden" name="'+aid+'[]" id="fid_val_'+aids+'" value="'+aids+'" />';
				  	if(aids==undefined){
				  		datas ='<input type="hidden" name="'+aid+'[]" id="fid_val" value="0" />';  	
				  		datas+='<input type="hidden" name="'+inputid+'_url" value="'+filedata+'" />'	
				  	}
                }else{
                	datas +='<input type="hidden" name="'+aid+'[]" id="fid_val_'+aids+'" value="'+aids+'" />';
	                if (filedata){
	                    imgdata += '<div id="aidimg_'+aids+'" style="position:relative;"><img src="' + filedata + '"/><span class="icon_del" style="position:absolute;top:0;right:0;z-index:2;"><a href="javascript:clean_thumb(\''+aids+'\');"><img style="cursor:pointer" src="/Public/images/icon/no.gif"></a></span></div>';
	                }
                }
            });
            if(isreplace==1){
            	$('#'+inputid).attr("src", filedata);
            	
            }else{
            	$('#'+inputid+'_pic').html(imgdata);
            }
            	

			oldaidhtml = $('#' + inputid + '_aid_box').html();
			$('#'+inputid+'_aid_box').html((isreplace == 1 ? '':oldaidhtml) + datas);
		}
}

function up_image(iframeWin, topWin,id,inputid){
		var num = iframeWin.$('#myuploadform > div').length;
		if(num){
			var aids = iframeWin.$('#myuploadform #aids').attr("value");
			var status = iframeWin.$('#myuploadform #status').attr("value");
			var filedata = iframeWin.$('#myuploadform #filedata').attr("value");
			var namedata = iframeWin.$('#myuploadform #filedata').attr("value");

			if(filedata){
				$('#'+inputid+'_pic').attr('src',filedata);
				$('#'+inputid).val(filedata);
				if(status==0) $('#'+inputid+'_aid_box').html('<input type="hidden"  name="aid[]" value="'+aids+'"  />');
			}
		}
}

function up_images(iframeWin, topWin,id,inputid){
		var data = '';
		var aidinput='';
		var num = iframeWin.$('#myuploadform > div').length;
		if(num){
			iframeWin.$('#myuploadform  div ').each(function(){
					var status =  $(this).find('#status').val();
					var aid = $(this).find('#aids').val();
					var src = $(this).find('#filedata').val();
					var name = $(this).find('#namedata').val();
					if(status==0) aidinput = '<input type="hidden" name="aid[]" value="'+aid+'"/>';
					data += '<div id="uplist_'+aid+'">'+aidinput+'<input type="text" size="50" class="input-text" name="'+inputid+'[]" value="'+src+'"  />  <input type="text" class="input-text" name="'+inputid+'_name[]" value="'+name+'" size="30" /> &nbsp;<a href="javascript:remove_this(\'uplist_'+aid+'\');">移除</a> </div>';
			});
			$('#'+inputid+'_images').append(data);
		}
}

function insert2editor(iframeWin, topWin,id,inputid){
		var img = '';
		var data = '';
		var num = iframeWin.$('#myuploadform > div').length;
		if(num){
				iframeWin.$('#myuploadform   div').each(function(){
					var status =  $(this).find('#status').val();
					var aid = $(this).find('#aids').val();
					var src = $(this).find('#filedata').val();
					var name = $(this).find('#namedata').val();
					if(status==0) data += '<input type="text" name="aid[]" value="'+aid+'"/>';
					img += IsImg(src) ?  '<img src="'+src+'" /><br />' :  (IsSwf(src) ? '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"><param name="quality" value="high" /><param name="movie" value="'+src+'" /><embed pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" src="'+src+'" type="application/x-shockwave-flash" width="460"></embed></object>' :'<a href="'+src+'" />'+src+'</a><br />') ;
			   });

			   $('#'+inputid+'_aid_box').append(data);
		}
		CKEDITOR.instances[inputid].insertHtml(img);
}


function upokis(arrMsg){
	//$('#'+arrMsg[0].editorid+'_aid_box').show();
	var i,msg;
	for(i=0;i<arrMsg.length;i++)
	{
		msg=arrMsg[i];
		if(msg.id>0)$('#'+msg.editorid+'_aid_box').append('<input type="text" name="aid[]" value="'+msg.id+'"/>');
		//$("#uploadList").append('<option value="'+msg.id+'">'+msg.localname+'</option>');
	}

}

function upok(id,data){
		$('#'+id+'_aid_box').append('ddddddddddddddddd');
		$('#'+id+'_aid_box').show();
}
function nodo(iframeWin, topWin){
	art.dialog.close();
}


function IsImg(url){
	  var sTemp;
	  var b=false;
	  var opt="jpg|gif|png|bmp|jpeg";
	  var s=opt.toUpperCase().split("|");
	  for (var i=0;i<s.length ;i++ ){
		sTemp=url.substr(url.length-s[i].length-1);
		sTemp=sTemp.toUpperCase();
		s[i]="."+s[i];
		if (s[i]==sTemp){
		  b=true;
		  break;
		}
	  }
	  return b;
}

function IsSwf(url){
	  var sTemp;
	  var b=false;
	  var opt="swf";
	  var s=opt.toUpperCase().split("|");
	  for (var i=0;i<s.length ;i++ ){
	    sTemp=url.substr(url.length-s[i].length-1);
	    sTemp=sTemp.toUpperCase();
	    s[i]="."+s[i];
	    if (s[i]==sTemp){
	      b=true;
	      break;
	    }
	  }
	  return b;
}

//单文件上传========================
function fileQueueError(file, errorCode, message) {
	try {
		var errorName = "";
		if (errorCode === SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED) {
			errorName = "你上传的文件太多了！";
		}

		if (errorName !== "") {
			alert(errorName);
			return;
		}
		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			alert("文件格式不正确！");
			break;
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			alert("文件超出大小！");
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
		default:
			alert(message);
			break;
		}

	} catch (ex) {
		this.debug(ex);
	}

}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesQueued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadProgress(file, bytesLoaded) {

	try {
		var percent = Math.ceil((bytesLoaded / file.size) * 100);
		var progress = new FileProgress(file,  this.customSettings.upload_target);
		progress.setProgress(percent);
		progress.appear();
		if (percent === 100) {
			progress.setStatus("生成内容...");
			progress.toggleCancel(false, this);
		} else {
			progress.setStatus("上传中...");
			progress.toggleCancel(true, this);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadContentComplete(serverData) {
	if (serverData.html != undefined) {
		return serverData.html;
	}
	return '';
}

function uploadSuccess(file, serverData) {
	try {
		// 需要jquery插件的支持
		var reutrn_data = $.parseJSON(serverData);
		var server_data = reutrn_data.data;
		var progress = new FileProgress(file,  this.customSettings.upload_target);
		if (server_data != undefined) {
			var content;
			if (typeof this.getSetting("content_complete_handler") === "function") {
				content = this.getSetting("content_complete_handler").apply(this, [server_data]);
			} else {
				throw "Event handler " + this.getSetting("content_complete_handler") + " is unknown or is not a function";
			}
			
			//content = this.queueEvent("content_complete_handler", serverData);
			var html = $(content).wrap("<div></div>").parents('div').addClass('upload-content-item');
			$(html).append("<span class='icon-del'><img style='cursor:pointer' /></span>")
				   .find('.icon-del>img')
				   .attr('src', "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAAOABADASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD7d/b6/aB+In7Ff7X2h+PL3xJrMngDxLeW0FtaRTtcWtrFHAn2q0ewaWNGkZUmnScMH3OEMiqqq7f+CZ37QPxR/bf/AGr7rx9d67fWPhLw/JNb6pp0eoP/AGdI0tq3k2UNoTtym+KZrll8xim3dtdkj0v2j/8AgnDq/wC1l+1tP4l8X+KIm8Bx2sP9n2cDyG+t0WOLzrNVICQpKwklNwrtJlgmzCo6Xv2NP+CcOvfso/tw2niLwv4jtI/h9c29wtxaSzStqF3C8EphtJU2eW4ilZXW4L+ZiPbt+eR2+IjQzH+1FUu/Yc23Nrfv/h8r/wCR+9vNOF3wm8Mox/tD2Px+zfLy3+H/AK+W+3t+Z//Z")
				   .click(function() {
					   $(this).parents(".upload-content-item").remove();
		   		});
			
			if (this.getSetting("file_field_name") != '') {
				$("<input type='hidden'/>").attr('name', this.getSetting("file_field_name"))
										   .attr('value', 'fid-' + server_data.fid)
										   .appendTo(html);
			}
			if (server_data.isimage != undefined) {
				$(html).addClass('upload-image-item');
			}

			$(this.getSetting("file_content_container")).append(html);
			$(html).fadeIn("slow");
			
			progress.setStatus("已生成内容.");
			progress.toggleCancel(false);
		}
		else {
			progress.setStatus("上传错误");
			progress.toggleCancel(false);
			alert(serverData);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadComplete(file) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus("已上传全部文件");
			progress.toggleCancel(false);
			progress.disappear();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	var progress;
	try {
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("已取消上传");
				progress.toggleCancel(false);
			}
			catch (ex1) {
				this.debug(ex1);
			}
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			try {
				progress = new FileProgress(file,  this.customSettings.upload_target);
				progress.setCancelled();
				progress.setStatus("已停止上传");
				progress.toggleCancel(true);
			}
			catch (ex2) {
				this.debug(ex2);
			}
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			alert("上传数量限制！");
			break;
		default:
			alert(message);
			break;
		}

	} catch (ex3) {
		this.debug(ex3);
	}
	
}

/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(file, targetID) {
	this.fileProgressID = "divFileProgress";

	this.fileProgressWrapper = document.getElementById(this.fileProgressID);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.fileProgressID;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(file.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		if (document.getElementById(targetID) != undefined) {
			document.getElementById(targetID).appendChild(this.fileProgressWrapper);
		}
		$(this.fileProgressWrapper).fadeIn("slow");

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = file.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;
}
FileProgress.prototype.setProgress = function (percentage) {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function () {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setError = function () {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setCancelled = function () {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

};
FileProgress.prototype.setStatus = function (status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
};
FileProgress.prototype.toggleCancel = function (show, swfuploadInstance) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (swfuploadInstance) {
		var fileID = this.fileProgressID;
		this.fileProgressElement.childNodes[0].onclick = function () {
			swfuploadInstance.cancelUpload(fileID);
			return false;
		};
	}
};
FileProgress.prototype.appear = function () {
	this.fileProgressWrapper.style.display = "";
};
FileProgress.prototype.disappear = function () {
	this.fileProgressWrapper.style.display = "none";
};

var ___swfupload_settings = {
		// Backend Settings
		//upload_url: "{:U('/Sys/Upload/'.FILE_USAGE_SUPPLIER_TRAIL)}",
		//post_params: { "{$Think.config.var_session_id}": "{:session_id()}", "type" : "small"},
		file_post_name : "filedata",
		// File Upload Settings
		file_size_limit : "2 MB",	// 2MB
		/*				use_query_string : true,*/
		file_types : "*;",
		file_types_description : "*",
		file_upload_limit : "5",

		// Event Handler Settings - these functions as defined in Handlers.js
		//  The handlers are not part of SWFUpload but are part of my website and control how
		//  my website reacts to the SWFUpload events.
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		// Button Settings
		button_placeholder_id : "uploadButtonPlaceholder",
		button_width: 150,
		button_height: 18,
		button_text : '<span class="button">上传 <span class="buttonSmall">(文件大小不超过2M)</span></span>',
		button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
		button_text_top_padding: 0,
		button_text_left_padding: 8,
		//button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
//		button_cursor: SWFUpload.CURSOR.HAND || '',

		// Flash Settings
		flash_url : "swfupload/swfupload.swf",

		// Custom Settings
		custom_settings : {
			upload_target : "divFileProgressContainer"
		},
		file_content_container : "#thumbnails",
		file_field_name : "attachments[]",

		// Debug Settings
		debug: false
	};

// 通用SwfUpload控件生成
function createSWFUpload(settings) {
	var swfobject, __settings = {};
	
	$.extend(__settings, ___swfupload_settings, settings);
	
	swfobject = new SWFUpload(__settings);
	
	if (swfobject.getSetting("content_complete_handler") == '') {
		swfobject.addSetting("content_complete_handler", uploadContentComplete);
	}
	if (swfobject.getSetting("file_content_container") == '') {
		swfobject.addSetting("file_content_container", "#upload_thumbnails");
	}
	
	$(swfobject.getSetting("file_content_container"))
		.children('.upload-content-item')
		.each(function() {
			$(this).append("<span class='icon-del'><img style='cursor:pointer' /></span>")
			   .find('.icon-del>img')
			   .attr('src', "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAAOABADASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD7d/b6/aB+In7Ff7X2h+PL3xJrMngDxLeW0FtaRTtcWtrFHAn2q0ewaWNGkZUmnScMH3OEMiqqq7f+CZ37QPxR/bf/AGr7rx9d67fWPhLw/JNb6pp0eoP/AGdI0tq3k2UNoTtym+KZrll8xim3dtdkj0v2j/8AgnDq/wC1l+1tP4l8X+KIm8Bx2sP9n2cDyG+t0WOLzrNVICQpKwklNwrtJlgmzCo6Xv2NP+CcOvfso/tw2niLwv4jtI/h9c29wtxaSzStqF3C8EphtJU2eW4ilZXW4L+ZiPbt+eR2+IjQzH+1FUu/Yc23Nrfv/h8r/wCR+9vNOF3wm8Mox/tD2Px+zfLy3+H/AK+W+3t+Z//Z")
			   .click(function() {
				   $(this).parents(".upload-content-item").remove();
			   });
		});
	
	return swfobject;
}

