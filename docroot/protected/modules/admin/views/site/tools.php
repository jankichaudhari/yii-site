<?php
/**
 * @var $this SiteController
 */
?>
<style type="text/css">
	div.list {

	}

	div.list .header {
		padding     : 7px;
		color       : white;
		font-size   : 16px;
		font-weight : bold;
		background  : #f90;
	}

	div.list .list-row {
		padding       : 3px 7px;
		border-bottom : 1px solid #dedede;
	}

	div.list .list-row a {
		color           : #0054ff;
		text-decoration : none;
		font-weight     : bold;
	}
</style>
<div class="row-fluid">
	<div class="span6">
		<div class="list">
			<div class="header">Tools</div>
			<div class="list-row"><?php echo CHtml::link('User Preferences', ['User/UserPreferences']) ?></div>
			<div class="list-row"><?php echo CHtml::link('Staff list', ['User/staff']) ?></div>
			<div class="list-row"><?php echo CHtml::link('User Preferences', ['User/UserPreferences']) ?></div>
			<div class="list-row"><?php echo CHtml::link('Board Management', '/v3.0/live/admin/board.php') ?></div>
			<div class="list-row"><?php echo CHtml::link('QuickReports', ['quickReport/list']) ?></div>
			<div class="list-row"><?php echo CHtml::link('Portal Feeds', ['feed/run']) ?></div>
			<div class="list-row"><?php echo CHtml::link('Mailshot types', ['mailshotType/']) ?></div>
			<div class="list-row"><?php echo CHtml::link('Missing Feedback (works extremely slow. never used)', '/v3.0/live/admin/appointment_nofeedback.php') ?></div>
			<div class="list-row"><?php echo CHtml::link('Old tools page', '/v3.0/live/admin/tools.php') ?></div>


		</div>
	</div>

	<div class="span6">
		<div class="list">
			<div class="header">Public website tools</div>
			<div class="list-row"><?php echo CHtml::link('Map & Transport Control panel', ['transportStations/']) ?></div>
			<div class="list-row"><?php echo CHtml::link('Contact page gallery', ['file/PageGalleryImage']) ?></div>
			<div class="list-row"><?php echo CHtml::link('Manage Videos Sequence', ['instructionVideo/manageVideo']) ?></div>
		</div>
	</div>
</div>