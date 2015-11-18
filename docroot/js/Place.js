
function toggleChecked(status) {
	$(".checkImage").each( function() {
		$(this).attr("checked",status);
	})
}

function deleteSelected(record)
{
	var countChecked = $('input.checkImage:checked').length;
	if(countChecked!=0)
	{
		if(!confirm("Are you sure you want to delete selected image(s) (It could be used in this description)?")) {
			return false;
		}
		$('input.checkImage:checked').each( function() {
			var thisId = $(this).val();
			var multiple = true;
			deleteImage(thisId,'GalleryImage',multiple);
		})
	} else {
		alert("Please select image(s)");
		return false;
	}
}

/*On Change Event of Image selector*/
var submitImage = function ()
{
	$('#place-form').submit();
	return true;
}


var deleteImage = function(id,imageType,multiple) {
	if(!multiple){
		$msg = '';
		if(imageType=='GalleryImage'){
			$msg = ' (It could be used in this description )';
		}
		if(!confirm("Are you sure you want to delete this image "+$msg+"?")) { return false; }
	}

	$.get('/admin4/File/delete/id/' + id + '/fileModel/'+imageType+'/recordModel/Place/' , function(data) {
		if(data.result == true) {
			var thisEle = imageType+'_Preview';
			if(imageType=='GalleryImage'){
				thisEle = "placeImgPreview-" + id;
			}
			$("#" + thisEle).hide();
		}
	},"json");
}

