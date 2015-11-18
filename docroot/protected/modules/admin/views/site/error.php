<?php
/**
 * @var $error
 */
?>
	<h1>Sorry your request is invalid</h1>
	<h2>Code: <?php echo $error['code'] ?></h2>
	<h2>Type: <?php echo $error['type'] ?></h2>
	<h2>message: <?php echo $error['message'] ?></h2>
<?php if (Yii::app()->user->id): ?>
	<h2>File: <?php echo $error['file'] ?>:<?php echo $error['line'] ?></h2>
	<h2>Line: <?php echo $error['line'] ?></h2>
	<h2>Trace:</h2>
	<pre><?php echo $error['trace'] ?></pre>
<?php endif ?>