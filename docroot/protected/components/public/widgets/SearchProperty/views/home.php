<?php
/**
 * @var $model     Deal
 * @var $this      SearchProperty
 * @var $form      CActiveForm
 * @var $type      String Sales
 * @var $minPrices Array
 * @var $maxPrices Array
 * @var $isMobile bool
 */
$deal = isset($_GET['Deal']) ? $_GET['Deal'] : null;
?>
<?php $form = $this->beginWidget('CActiveForm', Array(
		'method' => 'get',
		'action' => '/sales'
)) ?>
<div class="top-widget-container narrow home-search-property">
	<div class="inner-padding">
		<div class="row-fluid">
			<div class="form-header">
				Search Property
			</div>
			<div class="full-width-input-wrapper">
				<?php
				$fullAddressString = isset($_GET['Property']['fullAddressString']) ? $_GET['Property']['fullAddressString'] : '';
				echo CHtml::textField('Property[fullAddressString]', $fullAddressString, [
						'class'       => 'search input-large',
						'placeholder' => 'Search'
				]);
				?>
			</div>
		</div>
		<div class="row">
			<div class="half-cell">
				<label class="block-label">
					Min Bed
				</label>

				<div class="input-wrapper">
					<?php
					$minBed = isset($deal['min_bedrooms']) ? $deal['min_bedrooms'] : "";
					echo CHtml::dropDownList('Deal[min_bedrooms]', $minBed, ['Studio', 1, 2, 3, 4, 5, 6]);
					?>
				</div>
			</div>
			<div class="half-cell">
				<label class="block-label">
					Max Bed
				</label>

				<div class="input-wrapper">
					<?php
					$maxBed = isset($deal['max_bedrooms']) ? $deal['max_bedrooms'] : "";
					echo CHtml::dropDownList('Deal[max_bedrooms]', $maxBed, [
							'Studio', 1, 2, 3, 4, 5, 6
					], ['empty' => 'Max']);
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="half-cell">
				<label class="block-label">
					Min Price
				</label>

				<div class="input-wrapper">
					<?php
					$minPrice = isset($deal['min_price']) ? $deal['min_price'] : "";
					echo CHtml::dropDownList('Deal[min_price]', $minPrice, $minPrices, ['empty' => 'No Minimum'])
					?>
				</div>
			</div>
			<div class="half-cell">
				<label class="block-label">
					Max Price
				</label>

				<div class="input-wrapper">
					<?php
					$maxPrice = isset($deal['max_price']) ? $deal['max_price'] : "";
					echo CHtml::dropDownList('Deal[max_price]', $maxPrice, $maxPrices, ['empty' => 'No Maximum']);
					?>
				</div>
			</div>
		</div>
		<div class="row mobile-row-half-cell">
			<div class="cell right">
				<label class="block-label hide">Show</label>
				<div class="input-wrapper">
					<?php
					$allValue = 'all';
					$availableValue = 'available';
					$allChecked = isset($deal['showMode']) ? ($deal['showMode'] == $allValue ? true : false) : true;
					$availableChecked = (isset($deal['showMode']) && $deal['showMode'] == $availableValue) ? true : false;

					if ($isMobile):
						echo CHtml::dropDownList('Deal[showMode]', $availableChecked ? $availableValue : $allValue, [
								$allValue       => 'All',
								$availableValue => 'Available'
						]);
					else:
						?>
						<label>
							<?php
							$allValue = 'all';
							$allChecked = isset($deal['showMode']) ? ($deal['showMode'] == $allValue ? true : false) : true;
							echo CHtml::radioButton('Deal[showMode]', $allChecked, [
									'value' => $allValue,
									'class' => 'no-margin',
									'id'    => 'Deal_showMode_All'
							]);
							?>
							<label class="radio-label no-margin" for="Deal_showMode_All">All</label>
						</label>
						<label>
							<?php
							$availableValue = 'available';
							$availableChecked = (isset($deal['showMode']) && $deal['showMode'] == $availableValue) ? true : false;
							echo CHtml::radioButton('Deal[showMode]', $availableChecked, [
									'value' => $availableValue,
									'class' => 'left-margin',
									'id'    => 'Deal_showMode_Available',
									'style' => 'margin-left: 3px'
							]);
							?>
							<label class="radio-label no-margin" for="Deal_showMode_Available">Available</label>
						</label>
					<?php
					endif;
					?>
				</div>
			</div>
		</div>
		<div class="row mobile-row-half-cell">
			<div class="cell">
				<label class="block-label hide">&nbsp;</label>
				<input type="submit" value="Search" class="btn full-width huge-orange-button">
			</div>
		</div>

	</div>
</div>

<?php $this->endWidget() ?>
