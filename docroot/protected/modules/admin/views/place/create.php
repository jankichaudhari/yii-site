<?php
/**
 * @var $location
 * @var $galleryImageError
 * @var $newRecord
 * @var $mainGalleryImage
 */
?>
<?php echo $this->renderPartial('_form',
								array(
									 'model'             => $model,
									 'location'          => $location,
									 'galleryImageError' => $galleryImageError,
									 'mainViewImage'     => $mainViewImage,
									 'mainGalleryImage'  => $mainGalleryImage,
									 'newRecord'         => $newRecord,
								)); ?>