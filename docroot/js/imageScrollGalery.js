/**
 * Created with JetBrains PhpStorm.
 * User: vitaly.suhanov
 * Date: 06/08/12
 * Time: 16:13
 * To change this template use File | Settings | File Templates.
 */

jQuery.fn.imageScrollGallery = function (options)
{
	var options = jQuery.extend({leftArrow     : '.arrow-left',
		rightArrow : '.arrow-right',
		imgWidth   : 128,
		imgHeight  : 128,
		gap        : 3,
		scrollFor  : 5,
		duration   : 500
	}, options);
	$(options.leftArrow).hide();

	var size = $('img', this).size();
	if (size <= options.scrollFor) {
		$(options.rightArrow).hide();
	}
	width = size * options.imgWidth + options.gap * size;

	$(this).width(width);

	var self = this;

	var inProgress = false;
	var continueFunction = function ()
	{


		inProgress = false;
	};
	var moveRight = function ()
	{
		if (inProgress) return;
		inProgress = true;
		margin = $(self).css('marginLeft').replace('px', '');
		var offset = (options.imgWidth + options.gap) * options.scrollFor;
		$(options.leftArrow).show();
		$(options.rightArrow).show();
		if (offset >= width - offset - Math.abs(margin)) {
			offset = width - offset - Math.abs(margin);
			$(options.rightArrow).hide();
		}

		$(self).animate({
			'marginLeft' : '-=' + offset
		}, options.duration, continueFunction);
	}

	var moveLeft = function ()
	{
		if (inProgress) return;
		inProgress = true;
		margin = $(self).css('marginLeft').replace('px', '');
		var offset = (options.imgWidth + options.gap) * options.scrollFor;
		$(options.leftArrow).show();
		$(options.rightArrow).show();
		if (Math.abs(margin) <= Math.abs(offset)) {
			offset = Math.abs(margin);
			$(options.leftArrow).hide();
		}


		$(self).animate({
			'marginLeft' : '+=' + offset
		}, options.duration, continueFunction);
	}

	$(options.rightArrow).on('click', moveRight);
	$(options.leftArrow).on('click', moveLeft);
};
