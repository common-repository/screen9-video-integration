jQuery(document).ready(function ($) {

$("object").ready(function() {
	setInterval(function(){getEncodingStatus()},3000);    
});

$('#video_upload_btn').click(function(){
	$('#post_name').remove();
    var formData = new FormData($('#upload_form')[0]);
    var action = $('#upload_form').attr('action');
    $.ajax({
        url: action, 
        type: 'POST',
        xhr: function() { 
            var uploadXhr = $.ajaxSettings.xhr();
            if(uploadXhr.upload){ 
                uploadXhr.upload.addEventListener('progress',progressHandlingFunction, false);
            }
            return uploadXhr;
        },
        
        complete: function() {
        	$('form[name=post]').submit();
        },
        
        data: formData,
        
        cache: false,
        contentType: false,
        processData: false
    });
});

function progressHandlingFunction(e){
    if(e.lengthComputable){
        $('progress').attr({value:e.loaded,max:e.total});
    }
}

function getEncodingStatus(){

	var mediaid = $('#videoid').val();

	if(mediaid !=''){
		var data = {
			action: "screen9_video_status",
		    videoid: mediaid
		};
		 $.post(		      
		      ajax_object.ajax_url,
		      data,
		      function(response)
		      {
		    	  if(typeof response.status != "undefined"){
		    		  $('progress').attr({value:response.processing_progress.progress,max:1});
		    		  $('#statustext').text(response.status);
		    	  }
		      });
	 }
}

if($('#videofile').val()==''){
	$('.button').mousedown(function(e) {
		alert(ajax_object.error_msg_upload);
		return false;
	});
}

});

