<div class="row-fluid page-header">
	<div class="span6">
		<div class="page-title"><?php echo CHtml::link(Yii::app()->user->getState("fullname"), array("User/UserPreferences")); ?></div>
	</div>
	<div class="span6">
		<div class="page-title text-right"><?php echo date("l jS F Y") ?> <span id="timecontainer"></span></div>
	</div>
</div>
<div>

</div>
<?php
$js = '
	var updateTime = function (containerId)
	{
		var container = document.getElementById(containerId);
		if (!container) {
			return; // smthing wrong
		}
		var date = new Date();
		container.innerHTML = (function ()
		{
			var hours = date.getHours();
			var pm = hours > 12 || false;
			hours = hours > 12 ? hours - 12 : hours;
			return hours + ":" + ("0" + date.getMinutes()).substr(-2) + " " + (pm ? "PM" : "AM");
		})();
	}
	var updateTimeInTimecontainer = function ()
	{
		updateTime("timecontainer")
		setTimeout("updateTimeInTimecontainer", 60000);
	}
	updateTimeInTimecontainer();';
Yii::app()->getClientScript()->registerScript('header-timer', $js, CClientScript::POS_END);
?>