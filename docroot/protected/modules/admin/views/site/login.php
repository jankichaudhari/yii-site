<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="icon" type="image/png" href="/images/sys/favicon-admin.ico" />
	<meta name="language" content="en" />
	<link rel="stylesheet" href="/css/grey-smooth/default.css">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<style type="text/css">
		@media only screen and (max-width: 640px) {
			body, html {
				width  : 100%;
				margin : 0 auto;
			}
			input {
				width     : 100% !important;
				font-size : 50pt;
			}
			label.control-label {
				font-size : 50pt;
				line-height: 50pt;
				display: block !important;
				clear:both;
				float:none;
			}
			.controls {
				float: none;
				clear:both;
				display: block;
				margin-left: 0 !important;

			}

			.force-margin {
				margin-left: 0 !important;
				padding-left: 0 !important;
			}

			.btn {
				line-height: 50pt;
			}

			legend {
				font-size : 50pt;
			}
			div.wide.form .buttons, div.wide.form .hint, div.wide.form .errorMessage {
				clear        : left;
				padding-left : 0px;
			}
		}
	</style>
</head>
<body>
<?php
$form = $this->beginWidget('AdminForm', array(
											 'id'                     => 'login-form',
											 'enableClientValidation' => true,
											 'clientOptions'          => array(
												 'validateOnSubmit' => true,
											 ),
										)); ?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Login</div>

			<div class="content"><?= $form->beginControlGroup($model, 'username'); ?>
				<?= $form->controlLabel($model, 'username'); ?>
				<div class="controls">
					<?php echo $form->textField($model, 'username'); ?>
				</div>
				<?= $form->endControlGroup(); ?>
				<?= $form->beginControlGroup($model, 'password'); ?>
				<?= $form->controlLabel($model, 'password'); ?>
				<div class="controls">
					<?php echo $form->passwordField($model, 'password'); ?>
				</div>
				<?= $form->endControlGroup(); ?></div>
			<div class="block-buttons force-margin">
				<?php echo CHtml::submitButton('Login', array('class' => "btn")); ?>
			</div>
		</fieldset>
	</div>
</div>
<?php $this->endWidget(); ?>

</div>

</body>
<script type="text/javascript">
	document.getElementById("LoginForm_username").focus();
</script>
</html>