$('.tab-group').each(function ()
{
	var context = $(this);

	$('.tab-header', context).on('click', function ()
	{
		$('.tab-header', context).removeClass('active');
		$(this).addClass('active');

		var id = $(this).data('header-for');
		$('.tab', context).removeClass('active');
		$('#' + id + '', context).addClass('active');
	});
});