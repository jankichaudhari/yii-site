<?php
$pendSales = Client::model()->pendingCount("sales");
$pendLet = Client::model()->pendingCount("lettings");
?>
<div id="main_menu">
	<ul>
		<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>home.php">Home Page</a></li>
		<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>calendar.php">Calendar</a>
			<ul>
				<li><?php echo CHtml::link("Arrange Viewing", array('AppointmentBuilder/selectClient', 'for' => 'viewing')) ?></li>
				<li><?php echo CHtml::link("Arrange Valuation", array('AppointmentBuilder/selectClient', 'for' => 'valuation')) ?></li>
				<li><a href="<?php echo Yii::app()->params['globalUrlOld']; ?>appointment_search.php">Search Calendar</a></li>
			</ul>
		</li>
		<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>property.php">Property</a>
			<ul>
				<li><?= CHtml::link('Search Instruction', ['instruction/search', AdminFilterForm::generateResetParam('instruction-filter-form') => 'true']) ?></li>
				<li><?= CHtml::link('Search Property', ['property/select']) ?></li>
<!--				<li><a href="--><?//= Yii::app()->params['globalUrlOld']; ?><!--property_search.php">Search Instruction</a></li>-->
				<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>valuation_add.php">New Property</a></li>
			</ul>
		</li>
		<li><a href="#">Clients</a>
			<ul>
<!--				<li>--><?//= CHtml::link("Search Clients", array('Client/search')) ?><!--</li>-->
				<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>client_search.php">Search Clients</a></li>
				<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>new_client_pending_sales.php">Pending Sales (<?= $pendSales  ?>)</a></li>
				<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>new_client_pending_lettings.php">Pending Lettings (<?= $pendLet ?>)</a></li>
			</ul>
		</li>
		<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>contact.php">Contacts</a>
			<ul>
				<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>contact_add.php">New Contact</a></li>
				<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>company_add.php">New Company</a></li>
			</ul>
		</li>
		<li><a href="<?= Yii::app()->params['globalUrlOld']; ?>tools.php">Tools</a></li>
		<li class="hl"><a href="/admin">Old Admin</a></li>
		<?php if (Yii::app()->user->is("SuperAdmin")): ?>
			<li><?= CHtml::link('Super Admin', Array('SuperAdmin/')) ?>
			</li>
		<?php endif ?>
		<li>
			<?= CHtml::link("Listing", array("LocalEvent/")) ?>
			<ul>
				<li>
					<?= CHtml::link("Local Events", array("LocalEvent/")) ?>
				</li>
				<li>
					<?= CHtml::link("Places (Parks)", array("Place/")) ?>
				</li>
			</ul>
		</li>
		<li><?= CHtml::link("Quick Reports", array(false && Yii::app()->user->is("SuperAdmin") ? "QuickReport/List" : "")) ?>
			<ul>
				<?php if (Yii::app()->user->is("SuperAdmin")) : ?>
<!--					<li>--><?//= CHtml::link("List Quick Reports", array('QuickReport/List')) ?><!--</li>-->
				<?php endif ?>
				<li><?= CHtml::link("Missing EPCs", array('QuickReport/instructionsWithoutEPC')) ?></li>
				<?php
				//				foreach (QuickReport::model()->active()->findAll() as $key => $value) {
				//					echo '<li>' . CHtml::link($value->title, array('QuickReport/view', 'pk' => $value->name)) . '</li>';
				//				}
				?>
				<!--				--><?php //echo Yii::app()->user->is("SuperAdmin") ? '<li>' . CHtml::link("Create new report", array('QuickReport/create')) . '</li>' : '' ?>
			</ul>
		</li>
		<li><?= CHtml::link("Logout", array('site/logout')) ?></li>
	</ul>
</div>
