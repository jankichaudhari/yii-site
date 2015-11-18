<?php
/**
 * @var $this  CController
 * @var $model Property
 */
?>
<div class="row-fluid">
	<div class="span12">
		<fieldset>
			<div class="block-header">Select Instruction</div>

			<div class="content">
				<p>
					The property you chose (<?php echo $model->address->getFullAddressString(' ') ?>) already has associated instructions:
				</p>
				<ul>
					<?php foreach ($model->instructions as $key => $instruction): ?>
						<li>
							<a href="<?php echo $this->createUrl('instructionSelected', ['instructionId' => $instruction->dea_id]) ?>">
								<?php echo Date::formatDate('d/m/Y', $instruction->dea_created) ?>
								<?php echo $instruction->dea_type ?>
								<?php echo $instruction->dea_status ?>
								<?php if ($instruction->negotiator) : ?>
									created by <?php echo $instruction->negotiator->getFullName() ?>
								<?php endif ?>

							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="block-buttons">
				<a href="<?php echo $this->createUrl('', ['new' => true]) ?>" class="btn">Create new Instruction</a>
			</div>
		</fieldset>
	</div>
</div>