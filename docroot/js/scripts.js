function myInit()
{
	currentSalesId = 0;
	$('#indexPromoLatestSales > ol > li').hover(function ()
												{
													$('#indexPromoLatestSales > ol > li').removeClass('selected');
													$(this).addClass('selected');

													propId = this.id;
													propId = propId.replace('deal_', '');
													if (propId == currentSalesId) {
														return false;
													}
													loadLatest(propId, 'sales');

												}, function ()
	{
	});

	currentLettingsId = 0;
	$('#indexPromoLatestLettings > ol > li').hover(function ()
												   {
													   $('#indexPromoLatestLettings > ol > li').removeClass('selected');
													   $(this).addClass('selected');

													   propId = this.id;
													   propId = propId.replace('deal_', '');
													   if (propId == currentLettingsId) {
														   return false;
													   }
													   loadLatest(propId, 'lettings');

												   }, function ()
												   {

												   }
	);


	currentTop20Id = 0;
	$('#indexPromoTop20 > ol > li').click(function ()
										  {
											  $('#indexPromoTop20 > ol > li').removeClass('selected');
											  $(this).addClass('selected');

											  propId = this.id;
											  propId = propId.replace('deal_', '');
											  if (propId != currentTop20Id) {
												  loadTop20(propId);
												  return false;
											  }

										  }
	);

	$('#scrollUp').click(function ()
						 {
							 galleryScrollLeft();
							 return false;
						 });

	$('#scrollDown').click(function ()
						   {
							   galleryScrollRight();
							   return false;
						   });

	$('#thumbnailScroller > img').click(function ()
										{
											imageId = this.id;
											imageId = imageId.replace('thumb_', '');

											loadImage(imageId);
											return false;
										});


	$("#fname").focus(function ()
					  {
						  if ($("#fname").val() == "First Name(s)") {
							  $("#fname").val("");
						  }
					  });
	$("#sname").focus(function ()
					  {
						  if ($("#sname").val() == "Surname") {
							  $("#sname").val("");
						  }
					  });


}


function loadImage(imageId)
{

	if (!imageId) {
		return;
	}

	$('#mainImage').fadeOut(100, function ()
	{
		$.ajax({
				   type    : "POST",
				   url     : "/loadImage.php",
				   data    : "imageId=" + imageId,
				   success : function (returned)
				   {
					   myData = returned.split("~");
					   $('#mainImage').html(myData[0]);
					   $('#mainImage').fadeIn(100, function ()
					   {

					   });
				   }
			   });
	});
}


function loadLatest(propId, dept)
{

	if (!propId) {
		return;
	}
	if (dept == 'sales') {
		target = 'latestImageSales';
		currentSalesId = propId;
	}
	else {
		target = 'latestImageLettings';
		currentLettingsId = propId;
	}

	$('#' + target).fadeOut(0, function ()
	{
		$.ajax({
				   type    : "POST",
				   url     : "/loadLatest.php",
				   data    : "propId=" + propId,
				   success : function (returned)
				   {
					   $('#' + target).html(returned);
					   $('#' + target).fadeIn(0);
				   }
			   });
	});
}


function loadTop20(propId)
{

	if (!propId) {
		return;
	}

	target = 'top20Image';
	currentTop20Id = propId;

	$('#' + target).fadeOut(0, function ()
	{
		$.ajax({
				   type    : "POST",
				   url     : "/loadTop20.php",
				   data    : "propId=" + propId,
				   success : function (returned)
				   {
					   $('#' + target).html(returned);
					   $('#' + target).fadeIn(0);
				   }
			   });
	});
}


function galleryScrollLeft()
{
	if (scrollToInt > 0) {
		scrollToInt = parseInt(scrollToInt) - parseInt(scrollIncrement);
		scrollThumbnails(scrollToInt);
	}
}
function galleryScrollRight()
{
	if (scrollToInt < (maxWidth - paneWidth)) {
		scrollToInt = parseInt(scrollToInt) + parseInt(scrollIncrement);
		scrollThumbnails(scrollToInt);
	}
}

function scrollToImage(imageId)
{
	containerOffset = $('#thumbnails').offset();
	thumbOffset = $('#thumb_' + imageId).offset();
	scrollPixel = thumbOffset.left - containerOffset.left;
	scrollThumbnails(scrollPixel)
}

function scrollThumbnails(scrollPixel)
{

	if (scrollPixel < 0) {
		scrollPixel = 0;
	}

	$('#thumbnails').scrollTo(scrollPixel, { axis : 'y', duration : 500 });
	scrollToInt = parseInt(scrollPixel);
	if (scrollPixel >= (maxWidth - paneWidth)) {
		$('#scrollDown').fadeOut('normal');
	}
	if (scrollPixel > 0) {
		$('#scrollUp').fadeIn('normal');
		//$('#scrollUp').removeClass('disabled');
	}
	if (scrollPixel < (maxWidth - paneWidth)) {
		$('#scrollDown').fadeIn('normal');
		//$('#scrollDown').removeClass('disabled');
	}
	if (scrollPixel == 0) {
		$('#scrollUp').fadeOut('normal');
		//$('#scrollUp').addClass('disabled');
	}
}

function goback()
{
	history.go(-1);
}

function WSReset()
{
	top.location.href = '?'
}

function WSMax(myObj, myVar)
{
	minPrice = document.WS_Form.minp.selectedIndex;
	maxPrice = document.WS_Form.maxp.selectedIndex;
	intMaximum = (document.WS_Form.minp.length - 1)

	if (maxPrice > 0) {
		if (minPrice >= maxPrice) {
			if (minPrice == intMaximum) {
				document.WS_Form.maxp.selectedIndex = 0;
			}
			else {
				document.WS_Form.maxp.selectedIndex = (minPrice + 1);
			}
		}
	}
}

function WSMin(myObj, myVar)
{
	minPrice = document.WS_Form.minp.selectedIndex;
	maxPrice = document.WS_Form.maxp.selectedIndex;
	if (maxPrice > 0) {
		if (maxPrice <= minPrice) {
			document.WS_Form.minp.selectedIndex = (maxPrice - 1);
		}
	}
}

function WSPrint(propID)
{
	input_box = confirm("Click OK to see a printer friendly version of this property.\nA print dialogue box will appear automatically once the page has finished loading.\nWhen you are done please close the window to return to this page.\n\nnote: You may need to disable any popup blocking software for this to work");
	if (input_box == true) {
		window.open('/brochure/' + propID + '.html', 'PrintDetail', 'toolbar=yes,location=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=800,height=550');
	}
}


(function ($)
{
	var cache = [];
	// Arguments are image paths relative to the current page.
	$.preLoadImages = function ()
	{
		var args_len = arguments.length;
		for (var i = args_len; i--;) {
			var cacheImage = document.createElement('img');
			cacheImage.src = arguments[i];
			cache.push(cacheImage);
		}
	}
})(jQuery)