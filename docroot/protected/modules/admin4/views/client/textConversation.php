<?php
/**
 * @var $this          ClientController
 * @var $client        Client
 * @var $highlightText Sms|null
 */
$date = null;
?>
<style type="text/css">
	.conversation {
		width      : 50%;
		float      : left;
		margin-top : 70px;
	}

	.conversation::after {
		display : table;
		content : '';
	}

	.bubble-row {
		clear  : both;
		margin : 10px 0;
	}

	.bubble-row:after, .bubble-row:before {
		display : table;
		content : '';
	}

	.bubble-row:after {
		clear : both;
	}

	.conversation .date {
		text-align : center;
		font-size  : 11px;
		color      : #908f92;
		margin     : 10px 0;
	}

	.bubble {
		float         : right;
		max-width     : 40%;
		background    : #ffb039;
		border-radius : 15px;
		position      : relative;
		padding       : 10px;
		color         : white;
		cursor        : pointer;
		margin        : 10px 5px 10px 0;
	}

	.bubble.incoming {
		float        : left;
		background   : #e6e6eb;
		color        : #535356;
		margin-left  : 5px;
		margin-right : 5px;
	}

	.bubble .beak {
		position          : absolute;
		border            : 5px solid transparent;
		border-top        : 15px solid #ffb039;
		right             : -8px;
		bottom            : -13px;
		width             : 1px;
		height            : 1px;
		-webkit-transform : rotate(-45deg);
		transform         : rotate(-45deg);
	}

	.bubble.incoming .beak {
		border-top        : 15px solid #e6e6eb;
		left              : -8px;
		bottom            : -13px;
		-webkit-transform : rotate(45deg);
		transform         : rotate(45deg);
	}

	.app-info {
		float      : right;
		color      : #8b8b8b;
		font-size  : 11px;
		margin     : 10px 5px 10px 0;
		text-align : right;
	}

	.app-info a {
		text-decoration : none;
		color           : #ffb039 !important;
	}

	.highlight {
		background : #FF8618;
	}

	.highlight .beak {
		border-top-color : #FF8618;
	}

	.bubble.incoming.highlight {
		background : #ACACAC;
	}

	.bubble.incoming.highlight .beak {
		border-top-color : #ACACAC;
	}

	.message-info-container {
		width    : 40%;
		float    : right;
		position : fixed;
		height   : 500px;
		/*background : #DEDEDE;*/
		right    : 10px;
	}

	h2 {
		color : #555;
	}

	h2 a {
		text-decoration : none;
		color           : #ffb039 !important;
	}

	h2.conversation-header {
		/*width      : 100%;*/
		background  : white;
		padding-top : 30px;
		top         : 56px;
		margin      : 0;
	}

	dl.info dt, dl.info dd {
		margin  : 0;
		padding : 5px;
	}

	dl.info dt {
		float        : left;
		text-align   : right;
		font-weight  : bold;
		color        : #ffb039;
		margin-right : 10px;

		width        : 200px;
	}

	dl.info dd {
		margin-left  : 20px;
		padding-left : 10px;
		color        : #555;
	}

	.loading, .sending {
		background : url('/images/loading.gif') center;
		width      : 32px;
		height     : 32px;
		margin     : 0 auto;
		display    : none;
	}

	.reply textarea {
		width         : 100%;
		height        : 100px;
		border-radius : 4px;
	}

	.symbol-count {
		text-align : right;
		color      : #ababab;
		font-size  : 11px;
	}

	.symbol-count.exceed {
		color : #7e0000;
	}

	.telephone {
		color   : #555;
		display : block;
	}

</style>
<h2 class="conversation-header fixed">Conversation with <?php echo CHtml::link($client->getFullName(), ['client/edit', 'id' => $client->cli_id]) ?></h2>
<div class="conversation">
	<?php foreach ($client->textMessages as $key => $sms): ?>
		<?php if ($date !== Date::formatDate("Y-m-d", $sms->created)): ?>
			<?php $date = Date::formatDate("Y-m-d", $sms->created) ?>
			<div class="date"><?php echo Date::formatDate("d/m/y", $date) ?></div>
		<?php endif ?>
		<?php $this->renderPartial('sms/message-row', ['sms' => $sms]) ?>
	<?php endforeach; ?>
</div>
<div class="message-info-container">
	<h2 style="color: #555">Message information</h2>

	<div class="loading"></div>
	<div class="message-info"><span style="font-size: 12px; font-weight: bold; color: #555555;">Please select a message to display info</span></div>
	<div class="reply">
		<h2>Reply</h2>

		<form class="reply-form">
			<?php foreach ($client->telephones as $key => $phone): ?>
				<?php if (!Locale::isMobile($phone->tel_number)) continue ?>
				<label class="telephone">
					<input type="radio" name="to" value="<?php echo $phone->tel_number ?>" />
					<?php echo $phone->tel_number ?>
				</label>
			<?php endforeach; ?>
			<textarea name="text" class="reply-field"></textarea>
			<input type="hidden" name="clientId" value="<?php echo $client->cli_id ?>" />

			<div class="symbol-count">Symbol count: 0/160</div>
			<div>
				<input type="button" value="Send" class="btn btn-primary btn-large" id="send-message-button" />

				<div class="sending"></div>
			</div>
		</form>
	</div>
</div>
<?php $this->renderPartial('sms/info-template') ?>
<?php $this->renderPartial('sms/message-row-template') ?>
<script type="text/javascript">
	(function ()
	{
		var infoTemplateIncoming = $('#info-template-incoming').html();
		var infoTemplateOutgoing = $('#info-template-outgoing').html();
		var lastMessageDate = "<?php echo $date ?>";

		Date.createFromMysql = function (mysql_string)
		{
			if (typeof mysql_string === 'string') {
				var t = mysql_string.split(/[- :]/);
				return new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
			}
			return null;
		}

		var loadMessageInfo = function (id)
		{
			$('.loading').show();
			$('.message-info').hide();
			$.get('<?php echo $this->createUrl('sms/info') ?>', {id : id, ajax : true, markAsRead : true}, function (data)
			{
				var html = data.type == '<?php echo Sms::TYPE_INCOMING ?>' ? infoTemplateIncoming : infoTemplateOutgoing;
				for (key in data) {
					html = html.replace('{{' + key + '}}', data[key]);
				}
				$('.message-info').html(html);
				$('.loading').hide();
				$('.message-info').show();
			});
		}

		$('body').on('click', '.bubble', function ()
		{
			var $this = $(this);
			$('.highlight').removeClass('highlight');
			$this.addClass('highlight', {duration : 100});
			var id = this.id.replace('message-', '');
			loadMessageInfo(id);

		});

		$('.conversation-header').width($('.conversation').width());

		$('.reply-field').on('keyup', function ()
		{
			$('.symbol-count').removeClass('exceed');
			var $this = $(this);
			var symbolCount = $this.val().length;
			if (symbolCount > 160) {
				$('.symbol-count').addClass('exceed');
			}
			$('.symbol-count').html('Symbol count: ' + symbolCount + '/160');
		});

		<?php if($highlightText): ?>
		var el = $('#message-<?php echo $highlightText->id ?>');
		el.hide();
		el.fadeIn();
		el.addClass('highlight');
		loadMessageInfo(<?php echo $highlightText->id ?>);
		<?php endif ?>

		$('[name=to]').first().attr('checked', true);

		$('#send-message-button').on('click', function ()
		{
			var $this = $(this);
			var data = $('.reply-form').serialize();
			$this.hide();
			$('.sending').show();
			$.post('<?php echo $this->createUrl('sms/send') ?>', data, function (res)
			{
				if (res.created.split(' ')[0] !== lastMessageDate) {
					lastMessageDate = res.created.split(' ')[0];
					var date = new Date();
					$('.conversation').append('<div class="date">' + date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear() + '</div>')
				}
				var template = $('#message-row-template').html();
				for (key in res) {
					template = template.replace('{{' + key + '}}', res[key]);
				}
				$("html, body").animate({ scrollTop : $(document).height() }, "slow");
				$('.conversation').append(template);
				$('.conversation .bubble-row:last-child').fadeIn();
				$('.sending').hide();
				$this.show();
			});
		});

	})();
</script>