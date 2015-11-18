<?php
/**
 * @var $this                    PropertyController
 * @var $model                   Deal
 */
?>

<?php
if ($model->features) :
	$count = 0;
	?>
	<div class="info-box property-features">
		<div class="inner-padding">
			<div class="header">Features</div>
			<?php
			foreach ($model->features as $feature):
				$count ++ ;
				$class = $count == 1 ? 'first' : '';
				$class .= $count == count($model->features) ? 'last' : '' ;
				?>
				<div class="narrow-info-row <?php echo $class ?>"><?php echo $feature->fea_title ?></div>
			<?php
			endforeach;
			?>
		</div>
	</div>
<?php endif; ?>