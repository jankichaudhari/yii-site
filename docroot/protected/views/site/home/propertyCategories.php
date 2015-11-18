<?php
/**
 * @var $propertyCategories
 */
?>

<div class="row margin-bottom spaced row-property-categories">
	<div class="span4 property-types" data-id="/property/">
		<div class="button">
			<div class="button-text">Residential Property</div>
		</div>
		<div class="picture" style="background : url('/images/PropertyCategory_category.jpg') center no-repeat;">
			<div class="text">
				<a href="/property/" class="gray bold white-text-shadow">Residential Property »</a>
			</div>
		</div>
	</div>
	<?php foreach ($propertyCategories as $category): ?>
<?php
		$bgColour        = $category->bgColour ? : 'F90';
		$textColour      = $category->textColour ? : 'FFF';
		$hoverBgColour   = $category->hoverBgColour ? : 'FFF';
		$hoverTextColour = $category->hoverTextColour ? : '333';

		$_banner     = $category->getImageURIPath('_banner') ? : ''; // crazy code; crazy code.
		$bannerStyle = $_banner ? "background : url('" . $_banner . "') center no-repeat;" : "background:#" . $bgColour . ";";

		$_category    = $category->getImageURIPath('_category') ? : ''; // crazy code; crazy code.
		$pictureStyle = $_category ? "background : url('" . $_category . "') center no-repeat;" : "background:#" . $hoverBgColour . ";";
		?>
		<div class="span4 property-types" data-id="/property/category/<?= $category->id ?>">
			<div class="button" style="<?php echo $bannerStyle ?>">
				<?php if ($src = $category->getImageURIPath('_text')): ?>
					<img src="<?= $src ?>" alt="<?= $category->title ?>">
				<?php else: ?>
					<div class="button-text" style="color: #<?= $textColour ?>">
						<?php echo $category->getName(); ?>
					</div>
				<?php endif ?>
			</div>
			<div class="picture" style="<?php echo $pictureStyle ?>">
				<?php if ($src = $category->getImageURIPath('_category_text')) : ?>
					<img src="<?= $src ?>" alt="<?= $category->title ?>">
				<?php else : ?>
					<div class="text">
						<a href="/property/category/<?= $category->id ?>"
						   class="gray bold white-text-shadow" style="color: #<?= $hoverTextColour ?>">
							<?= $category->title ?> »
						</a>
					</div>
				<?php endif ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
