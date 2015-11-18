<?php
/**
 * @var $this SiteController
 */
?>
<h1>Hello, <?php echo Yii::app()->user->getUserObject()->getFullname() ?></h1>
<p>This is a guest login, You are not allowed to perform most of the actions. Here are options available for you:</p>
<ul>
	<li><?php echo CHtml::link('HMRC Expense Report', ['QuickReport/hmrc']) ?></li>
</ul>