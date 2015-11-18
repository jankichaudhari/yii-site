<?php
/**
 * @var $this  ClientController
 * @var $model PublicClientRegisterForm
 * @var $form  CActiveForm
 * @var $message
 * @var $email
 * @var $telephone
 */
?>
<form id="callback-form" action="" method="post" class="callback-box">
	<div class="top-widget-container narrow">
		<div class="inner-padding">
			<div class="row-fluid">
				<div class="message">
					<?php echo $message ? $message : '' ?>
				</div>
			</div>

			<div class="row callback-detail-box">
				<div class="cell">
					If you wish to discuss your requirements, please call one of <a href="/contact">our offices</a>
					<br>
					<input value="callback" name="Callback[id]" id="Callback_id" type="hidden">
					<input name="Callback[email]" id="Callback_email" type="hidden" value="<?php echo $email ?>">
				</div>
			</div>

			<div class="row callback-detail-box">
				<div class="half-cell">
					<div class="input-wrapper">
						<input name="Callback[telephone]" id="Callback_telephone" type="text" maxlength="40" value="<?php echo $telephone ?>"/>
					</div>
				</div>
				<div class="half-cell">
					<div class="input-wrapper">
						<input type="submit" name="callback" value="Call Me Back" class="btn full-width">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="half-cell">
					<div class="input-wrapper">
						<a href="/register" class="go-back full-width">Go Back</a>
					</div>
				</div>
				<div class="half-cell processing" style="display: none">
					<div class="input-wrapper">
						<img src="/images/sys/loading.gif" alt="">
					</div>
				</div>
			</div>

		</div>
	</div>
</form>

<script type="text/javascript">
		$("#callback-form").bind("submit", function () {
			$('.processing').show();
			$.ajax({
				type: "POST",
				cache: false,
				url: "/client/callback/",
				dataType: "json",
				data: $(this).serializeArray(),
				success: function (data) {
					$('#callback-form .message').html(data);
					$('#callback-form .callback-detail-box').hide();
					$('.processing').hide();
				}
			});

			return false;
		});
</script>