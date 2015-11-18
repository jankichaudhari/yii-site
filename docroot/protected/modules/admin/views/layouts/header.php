<div id="page_header">
	<table class="header">
		<tr>
			<td class="title"><?php echo CHtml::link(Yii::app()->user->getState("fullname"), array("User/UserPreferences")); ?>
				<span class="versionInfo">version: <?php echo Yii::app()->params['version'] ?> </span></td>
			<td class="title" style="text-align: right"><?php echo date("l jS F Y") ?>
				<span id="timecontainer"></span>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	var updateTime = function (containerId) {
		var container = document.getElementById(containerId);
		if (!container) {
			return; // smthing wrong
		}
		var date = new Date();
		container.innerHTML = (function () {
			var hours = date.getHours();
			var pm = hours > 12 || false;
			hours = hours > 12 ? hours - 12 : hours;
			return hours + ":" + ("0" + date.getMinutes()).substr(-2) + " " + (pm ? "PM" : "AM");
		})();
	}

	var updateTimeInTimecontainer = function () {
		updateTime("timecontainer")
		setTimeout("updateTimeInTimecontainer", 60000);
	}
	updateTimeInTimecontainer();

</script>