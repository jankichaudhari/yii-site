/**
 * Created by vitaly on 18/11/2013.
 */
(function ()
{
	var $body = $('body');
	var el = '<pre style="color: #555; padding:5px; position: absolute; display: none; top:150px; max-height: 800px; min-width: 500px; max-width: 800px; overflow: auto; min-height: 400px; background: #FFB; border: 1px solid #555; left: 500px;"></pre>';
	var $el = $(el).appendTo('body');

	$body.on('click', '.grid-superadmin-info', function (event)
	{
		$el.show();
		$el.html($(this).attr('title'));
		$el.css('left', (($(window).width() - $el.width()) / 2) + "px");
		event.preventDefault();
		event.stopPropagation();
	});

	$body.on('click', function ()
	{
		$el.hide();
	});

	$body.on('dblclick', '.grid-superadmin-info', function ()
	{
		document.location.href = $(this).attr('href');
		event.preventDefault();
	});
})();