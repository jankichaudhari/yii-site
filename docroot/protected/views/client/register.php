<?php
/**
 * @var $this  ClientController
 * @var $model PublicClientRegisterForm
 * @var $form  CActiveForm
 * @var $result array
 */

$this->pageTitle = "Register";
?>

<div class="register-page">
	<div class="page-top-block register">
		<div class="page-content">
			<div class="row">
			</div>
		</div>
	</div>

	<div class="body">

		<div class="page-content register">
			<div class="row margin-bottom"></div>
			<div class="row margin-bottom">
				<div class="span6 orange-big-heading">Register For Email Updates</div>
				<div class="span6 grey-heading-text">
					To register with Wooster & Stock please fill out the form below. One
					of our team will then contact you via either phone or email to discuss
					your requirements further. Once registered, the moment we release
					a new property that matches your criteria it will be emailed to you
					immediately. If it tickles your fancy, give us call and we’ll take you for
					a look around.
				</div>
			</div>

			<div class="orange-big-separator"></div>

			<div class="row margin-bottom">
				<div class="span8">
					<?php $this->renderPartial('_registerForm', [
																	  'model'       => $model,
																	  'result' => $result
															  ]
					);
					?>
				</div>
				<div class="span4 register-buttons">
					<div class="row-fluid">
						<div class="register-button" style="background: url('/images/register-sales-button.jpg')">
							<a href="/property">SALES</a>
						</div>
						<div class="register-button" style="background: url('/images/register-valuations-button.jpg')">
							<a href="/valuations">VALUATIONS</a>
						</div>
						<div class="register-button" style="background: url('/images/register-parks-button.jpg')">
							<a href="/parks">PARKS</a>
						</div>
					</div>
				</div>
			</div>


		</div>
	</div>
</div>