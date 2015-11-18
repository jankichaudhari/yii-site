<?php
/**
 * @var $this         OfferController
 * @var $offers       Offer[]
 * @var $instructionId
 */
?>
<div class="block-buttons">
	<?php
	echo CHtml::link("Submit Offer", array(
										  'offer/create',
										  'instructionId' => $instructionId,
//										  'callback'      => 'refreshList',
//										  'popup'         => true
									 ), array('id' => 'addOffer', 'class' => 'btn btn-green open-offer')); ?>
</div>
<div>
	<div id="offer-list">
		<?php if ($offers): ?>
			<div class="content">
				<table class="small-table">
					<tr>
						<th>Date</th>
						<th>Price</th>
						<th>Negotiator</th>
						<th>Client</th>
						<th>Status</th>
						<th></th>
					</tr>
					<?php foreach ($offers as $offer): ?>

						<tr <?php echo $offer->off_status == Offer::STATUS_ACCEPTED ? 'class="accepted-offer"' : '' ?>>
							<td><?php echo Date::formatDate('d/m/Y', $offer->off_timestamp) ?></td>
							<td><?php echo Locale::formatPrice($offer->off_price) ?></td>
							<td>
								<span class="negotiator-color"
									  style="background: #<?php echo $offer->negotiator->use_colour ?>"></span><?php echo $offer->negotiator->getFullName() ?>
							</td>
							<td>
								<?php
								$clientList = [];
								foreach ($offer->clients as $client) {
									$clientList[] = CHtml::link($client->getFullName(), Yii::app()->createUrl('admin4/client/update', ['id' => $client->cli_id]));
								}
								?>
								<?php echo implode(', ', $clientList) ?>
							</td>
							<td><?php echo $offer->off_status ?></td>
							<td>
								<?php
								$editAppUrl = $this->createUrl('offer/update', array(
																					'id'       => $offer->off_id,
																					'callback' => 'refreshList',
																					'popup'    => true,
																			   ));
								echo CHtml::link(
										  CHtml::image(Icon::EDIT_ICON, "Edit"),
										  '#', [
											   'class' => 'open-offer', 'onclick' => "openOffer('" . $editAppUrl . "')"
											   ]
								);
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		<?php endif ?>
	</div>
</div>
<script type="text/javascript">
	var refreshList = function () {
		$.get('<?php echo $this->createUrl('listOffers', ['instructionId' => $_GET['instructionId']]) ?>', function (data) {
			$('#offer-list').replaceWith($('#offer-list', data));
		});
	};
	var openOffer = function (url) {
		var popup = new Popup(url);
		popup.open();
		return false;
	};
</script>
