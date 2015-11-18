<?php
/**
 * @var $model Place
 * @var $this  ParkController
 * @var $form  CActiveForm
 * @var $mobile
 */

$place = isset($_GET['Place']) ? $_GET['Place'] : null;
?>
<?php $form = $this->beginWidget('CActiveForm', Array(
		'method' => 'get',
		'action' => '/parks'
)) ?>
	<div class="top-widget-container narrow">
		<div class="inner-padding">
			<div class="row-fluid">
				<div class="form-header">
					Search Park
				</div>
				<div class="full-width-input-wrapper">
					<?php
					$title = isset($place['title']) ? $place['title'] : null;
					echo CHtml::textField('Place[title]', $title, [
							'placeholder' => 'Street Or Postcode',
							'class' => 'input-large',
							'size' => 40
					]);
					?>
				</div>
			</div>
			<div class="row-fluid text-14 description">
				<div class="info-row first">
					One of the best things about London, whether you're visiting or living here, is its abundance of
					green
					spaces.
					What's more, spending the day exploring these majestic open spaces is absolutely free.
					Click through to read more about each park and view our detailed selection of photos
				</div>
			</div>
			<div class="row">
				<div class="cell">
					<label class="block-label">Sort By</label>

					<div class="input-wrapper">
						<?php $sorFieldList = [
								'' => 'Default', 'title ASC' => 'Alphabetic', 'postcode ASC' => 'Postcode'
						];
						$sortFieldVal = isset($place['sortField']) ? $place['sortField'] : '';
						?>
						<?php
						if ($mobile):
							echo CHtml::dropDownList(
									  'Place[sortField]',
									  $sortFieldVal,
									  $sorFieldList
							);
						else:
							echo CHtml::radioButtonList(
									  'Place[sortField]',
									  $sortFieldVal,
									  $sorFieldList,
									  array(
											  'class'        => 'no-margin',
											  'separator'    => '&nbsp;&nbsp;&nbsp;&nbsp;',
											  'labelOptions' => array('class' => 'radio-label right-margin')
									  )
							);
						endif;
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="cell">
					<label class="block-label">Show</label>

					<div class="input-wrapper">
						<?php
						$radioButtonList = Lists::model()->getList("PublicPlacesParkType");
						foreach ($radioButtonList as $thisKey => $radioButton) {
							if ($radioButton == 'None') {
								$radioButtonList[$thisKey] = 'All';
							}
						}
						$typeId = isset($place['typeId']) ? $place['typeId'] : 1;
						?>
						<?php
						if ($mobile):
							echo CHtml::dropDownList(
									  'Place[typeId]',
									  $typeId,
									  $radioButtonList
							);
						else:
							echo CHtml::radioButtonList(
									  'Place[typeId]',
									  $typeId,
									  $radioButtonList,
									  array(
											  'class'        => 'no-margin',
											  'separator'    => '&nbsp;&nbsp;&nbsp;&nbsp;',
											  'labelOptions' => array('class' => 'radio-label right-margin')
									  )
							);
						endif;
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="cell right">
					<input type="submit" value="SEARCH" class="btn half-width">
				</div>
			</div>

		</div>
	</div>

<?php $this->endWidget() ?>