<?php
/**
 * @var       $model     Deal
 * @var       $this      SearchProperty
 * @var       $form      CActiveForm
 * @var       $type      String Sales|Lettings
 * @var       $minPrices Array
 * @var       $maxPrices Array
 * @var       $isMobile bool
 */
$deal = isset($_GET['Deal']) ? $_GET['Deal'] : null;
?>
<div class="top-widget-container wide">
	<div class="inner-padding">
		<?php $form = $this->beginWidget('CActiveForm', [
				'method' => 'get',
				'action' => '/sales'
		]) ?>

		<div class="row-fluid">
			<div class="form-header">
				Search Property
			</div>
		</div>

		<div class="row">
			<div class="half-cell">
				<label class="block-label">Branch</label>

				<div class="input-wrapper">
					<?php
					$branch = isset($deal['dea_branch']) ? $deal['dea_branch'] : null;
					echo CHtml::dropDownList('Deal[dea_branch]', $branch, CHtml::listData(Office::model()->getShortBranchList($type), 'bra_id', 'shortTitle'), ['empty' => 'All']);
					?>
				</div>
			</div>
			<div class="half-cell">
				<label class="block-label">Address</label>

				<div class="input-wrapper">
					<?php
					$fullAddressString = isset($_GET['Property']['fullAddressString']) ? $_GET['Property']['fullAddressString'] : '';
					echo CHtml::textField('Property[fullAddressString]', $fullAddressString)
					?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="half-cell">
				<label class="block-label">Type of Property</label>

				<div class="input-wrapper">
					<?php
					$propertyType = isset($deal['dea_ptype']) ? $deal['dea_ptype'] : "";
					echo CHtml::dropDownList('Deal[dea_ptype]', $propertyType, CHtml::listData(PropertyType::model()
																										   ->getPublicSiteTypes($type), 'pty_id', 'pty_title'), ['empty' => 'All']);
					?>
				</div>
			</div>
			<div class="half-cell">
				<label class="block-label">Sort By</label>

				<div class="input-wrapper">
					<?php
					$sortBy = isset($deal['sort']) ? $deal['sort'] : "";
					echo CHtml::dropDownList('Deal[sort]', $sortBy,
											 [
													 'price DESC' => 'Highest Price',
													 'Price ASC'  => 'Lowest Price',
													 'date DESC'  => 'Date listed'
											 ]
					);
					?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="small-cell">
				<label class="block-label">Min Bed</label>

				<div class="input-wrapper">
					<?php
					$minBed = isset($deal['min_bedrooms']) ? $deal['min_bedrooms'] : "";
					echo CHtml::dropDownList('Deal[min_bedrooms]', $minBed, ['Studio', 1, 2, 3, 4, 5, 6]);
					?>

				</div>
			</div>
			<div class="small-cell">
				<label class="block-label">Max Bed</label>

				<div class="input-wrapper">
					<?php
					$maxBed = isset($deal['max_bedrooms']) ? $deal['max_bedrooms'] : "";
					echo CHtml::dropDownList('Deal[max_bedrooms]', $maxBed, [
							'Studio', 1, 2, 3, 4, 5, 6
					], ['empty' => 'Max']);
					?>
				</div>
			</div>
			<div class="small-cell">
				<label class="block-label">Min Price</label>

				<div class="input-wrapper">
					<?php
					$minPrice = isset($deal['min_price']) ? $deal['min_price'] : "";
					echo CHtml::dropDownList('Deal[min_price]', $minPrice, $minPrices, ['empty' => 'No Minimum'])
					?>
				</div>
			</div>
			<div class="small-cell">
				<label class="block-label">Max Price</label>

				<div class="input-wrapper">
					<?php
					$maxPrice = isset($deal['max_price']) ? $deal['max_price'] : "";
					echo CHtml::dropDownList('Deal[max_price]', $maxPrice, $maxPrices, ['empty' => 'No Maximum']);
					?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="half-cell">
				<label class="block-label">Show</label>

				<div class="input-wrapper">
					<?php
					$allValue = 'all';
					$availableValue = 'available';
					$allChecked = isset($deal['showMode']) ? ($deal['showMode'] == $allValue ? true : false) : true;
					$availableChecked = (isset($deal['showMode']) && $deal['showMode'] == $availableValue) ? true : false;
					?>
					<label>
						<?php
						echo CHtml::radioButton('Deal[showMode]', $allChecked, [
								'value' => $allValue,
								'class' => 'no-margin',
								'id'    => 'Deal_showMode_All'
						]);
						?>
						<label class="radio-label right-margin" for="Deal_showMode_All">All</label>
					</label>
					<label>
						<?php
						echo CHtml::radioButton('Deal[showMode]', $availableChecked, [
								'value' => $availableValue,
								'class' => 'left-margin',
								'id'    => 'Deal_showMode_Available'
						]);
						?>
						<label class="radio-label no-margin" for="Deal_showMode_Available">Available</label>
					</label>
				</div>
			</div>
			<div class="small-cell right">
				<label class="block-label">&nbsp;</label>
				<input type="submit" value="SEARCH" class="btn full-width">
			</div>
			<div class="small-cell right">
				<label class="block-label">&nbsp;</label>
				<input type="reset" value="RESET" class="btn full-width" onclick="location.href='/sales'">
			</div>
		</div>
		<?php $this->endWidget() ?>
	</div>
</div>
