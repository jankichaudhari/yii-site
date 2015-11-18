devnote = function (id)
{
	if (this == window) {
		return devnote.notes[id] || new devnote(id);
	}


	this.id = id;
	this.width = 300;
	this.height = 300;
	this.pageId = devnote.pageId;
	this.posX = 0;
	this.posY = 0;

	this.text = '';
	this.el = null;


	this.init = function (params)
	{
		for (option in params) {
			if (this.hasOwnProperty(option)) {
				this[option] = params[option];
			}
		}

		this.el = document.createElement('div');
		this.el.className = 'devnote_container';
		this.el.style.width = this.width + 'px';
		this.el.style.height = this.height + 'px';
		this.el.setAttribute('data-id', this.id);
		this.el.style.top = this.posY + 'px';
		this.el.style.left = this.posX + 'px';
		document.body.appendChild(this.el);

		var self = this;
		$(this.el).draggable({stop : function (event, ui)
		{
			self.posX = ui.position.left;
			self.posY = ui.position.top;
			self.save();
		}});
		$(this.el).html('<textarea class="devnote_container_textarea" style="height:' + (this.height-15) + 'px">' + this.text + '</textarea>');
		$(this.el).resizable({
								 stop   : function (event, ui)
								 {
									 self.width = ui.size.width;
									 self.height = ui.size.height;
//									 $('textarea', self.el).width = self.width;
									 $('textarea', self.el).height(self.height - 15);
									 self.save();
								 },
								 resize : function (event, ui)
								 {
									 self.width = ui.size.width;
									 self.height = ui.size.height;
									 $('textarea', self.el).height(self.height - 15);
								 }
							 });


	}

	this.resize = function (width, height)
	{
		this.width = width, height;

		this.el.style.width = this.width + 'px';
		this.el.style.height = this.height + 'px';

		this.save();
	}

	this.save = function ()
	{
		var self = this;
		console.log(this.text);
		jQuery.ajaxSetup({async : false});
		$.get('/admin4/devnote/save', {id : this.id, Devnote : {
			posX   : this.posX,
			posY   : this.posY,
			text   : this.text,
			pageId : this.pageId,
			width  : this.width,
			height : this.height
		}}, function (id)
			  {
				  self.id = id;
				  devnote.notes[id] = self;
			  })
		jQuery.ajaxSetup({async : true});

	}

	if (id == null) {
		this.save();
	} else {
		devnote.notes[id] = this;
	}


}
devnote.notes = {};
devnote.pageId = null;
devnote.toggleNotes = function ()
{
	$('.devnote_container').toggle();
};
$('body').on('change', '.devnote_container_textarea',
			 function ()
			 {

				 var self = this.parentNode.getAttribute('data-id');

				 self = devnote(self);
				 console.log(this.value);
				 self.text = this.value;
				 self.save();
			 });
devnote.new = function ()
{
	$('.devnote_container').show();
	devnote(null).init();
}
