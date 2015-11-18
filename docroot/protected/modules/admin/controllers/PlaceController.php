<?php
use \application\models\Place\GalleryImage as GalleryImage;
use \application\models\Place\MainGalleryImage as MainGalleryImage;
use \application\models\Place\MainViewImage as MainViewImage;

class PlaceController extends AdminController
{

	public $layout = '//layouts/adminDefault';
	public $placeImagesPaths;

	public function __construct($id, $module = null)
	{

		$this->placeImagesPaths = Yii::app()->params['imgPath'] . "/Place";
		parent::__construct($id, $module);
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'update' page.
	 */
	public function actionCreate()
	{

		$model     = new Place;
		$placeInfo = Place::model()->findAll('createdByUserId=' . Yii::app()->user->getId() . ' AND title is NULL');
		if (count($placeInfo) > 0) {
			foreach ($placeInfo as $place) {
				Place::model()->findByPk($place->id)->delete();
			}
		}
		$model->save(false);
		$this->redirect(array('update', 'id' => $model->id, 'newRecord' => true));

	}

	/**
	 * Updates a particular model.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id, $newRecord = false)
	{

		$this->layout      = '//layouts/adminDefault';
		$model             = $this->loadModel($id);
		$galleryImageError = false;
		$locationError     = false;
		$mainViewImage     = new MainViewImage();
		$mainGalleryImage  = new MainGalleryImage();

		if (!$model->location) {
			$location = new Location();
		} else {
			$location = $model->location;
		}

		if (isset($_POST['Location'])) {
			$location->attributes = $_POST['Location'];
			if (!empty($location->postcode)) {
				$location->scenario = 'locationRequire';
				if ($location->save()) {
					$model->addressId = $location->id;
				} else {
					$locationError = true;
				}
			}
		}

		if (isset($_POST['Place'])) {
			$model->attributes = $_POST['Place'];
			if (($model->statusId == 2) || ($model->statusId == 3)) {
				$model->scenario = 'goPlaceLive';

				if (isset($_POST['Location'])) {
					$location->scenario = 'locationRequire';
					if (!$location->validate()) {
						$locationError = true;
					}
				}

				if (isset($model->mainViewImageId) && !empty($model->mainViewImageId)) {
					$mainViewImage           = MainViewImage::model()->findByPk($model->mainViewImageId);
					$mainViewImage->scenario = 'viewImageCaption';
				}

				if (isset($model->mainGalleryImageId) && !empty($model->mainGalleryImageId)) {
					$mainGalleryImage           = MainViewImage::model()->findByPk($model->mainGalleryImageId);
					$mainGalleryImage->scenario = 'galleryImageCaption';
				}

				$allGalleryImages = GalleryImage::model()->findAllByAttributes([
																			   'recordId'   => $id,
																			   'recordType' => 'Place'
																			   ]);
				if (count($allGalleryImages) < 4) {
					$galleryImageError = 1;
				} else {
					foreach ($allGalleryImages as $galleryImages) {
						$galleryImages->scenario = 'imagesCaption';
						if (!$galleryImages->validate()) {
							$galleryImageError = 2;
						}
					}
				}
			} else {
				$locationError = false;
			}

			if (($model->validate()) &&
					($locationError == false) &&
					($mainViewImage->validate()) &&
					($mainGalleryImage->validate()) &&
					($galleryImageError == false) &&
					($model->save())
			) {
				Yii::app()->user->setFlash('success', 'Saved Successfully');
				$this->redirect(array('update', 'id' => $model->id));
			}
		}
		$this->render('update', array(
									 'model'             => $model,
									 'location'          => $location,
									 'galleryImageError' => $galleryImageError,
									 'mainViewImage'     => $mainViewImage,
									 'mainGalleryImage'  => $mainGalleryImage,
									 'newRecord'         => $newRecord,
								));
	}

	public function actionUpdateImages($id)
	{

		if (!empty($id)) {
			$model = $this->loadModel($id);
			$this->saveImages($id, 'GalleryImage', 'Place');
			$mainGalleryId = $this->saveImages($id, 'mainGalleryImage', 'PlaceMainGallery');
			if ($mainGalleryId) {
				if (!empty($model->mainGalleryImageId)) {
					if ($imageInfo = MainGalleryImage::model()->findByPk($model->mainGalleryImageId)) {
						$imageInfo->delete();
					}
				}
				$model->mainGalleryImageId = reset($mainGalleryId);
				$model->update(array('mainGalleryImageId'));
			}
			$mainViewId = $this->saveImages($id, 'mainViewImage', 'PlaceMainView');
			if ($mainViewId) {
				if (!empty($model->mainViewImageId)) {
					if ($imageInfo = MainViewImage::model()->findByPk($model->mainViewImageId)) {
						$imageInfo->delete();
					}
				}
				$model->mainViewImageId = reset($mainViewId);
				$model->update(array('mainViewImageId'));
			}
			$this->saveImages($id, 'placeImages', 'Place');

			$this->render('_updateImages', array(
												'model' => $model
										   ));
		}
	}

	public function actionSaveDescriptionImage($id)
	{

		if (!empty($id)) {
			$imageIds = $this->saveImages($id, 'upload', 'Place');
			reset($imageIds);
			$imageId       = current($imageIds);
			$thisImageInfo = GalleryImage::model()->findByPk($imageId);
			$imageFullPath = Yii::app()->params['imgUrl'] . "/Place/" . $id . "/" . $thisImageInfo->mediumName;

			if ($imageIds) {
				$funcNum = $_GET['CKEditorFuncNum'];
				?>
				<script type="text/javascript">
					window.parent.CKEDITOR.tools.callFunction("<?php echo $funcNum ?>", "<?php echo $imageFullPath ?>", function () {
					});
				</script>
				<?php
				return true;
			}
		}
	}

	public function saveImages($id, $instanceName, $recordType)
	{

		/** @var $images CUploadedFile[] */
		$images = CUploadedFile::getInstancesByName($instanceName);

		switch ($recordType) {
			case 'PlaceMainGallery' :
				$imagePath  = $this->placeImagesPaths . "/" . $id . '/' . $recordType;
				$imageModel = '\application\models\Place\MainGalleryImage';
				break;
			case 'PlaceMainView' :
				$imagePath  = $this->placeImagesPaths . "/" . $id . '/' . $recordType;
				$imageModel = '\application\models\Place\MainViewImage';
				break;
			default :
				$imagePath  = $this->placeImagesPaths . "/" . $id;
				$imageModel = '\application\models\Place\GalleryImage';
		}

		Yii::app()->file->set($imagePath)->createDir(0777);
		$ids             = array();
		$newResizeWidth  = 1280;
		$newResizeHeight = 1024;

		if ($images) {
			foreach ($images as $image => $pic) {
				$imageNameAr = explode(".", $pic->name);
//				$fileName = str_replace([" ","'","-",",","."], "_", reset($imageNameAr));
				$fileName  = preg_replace("/[^a-zA-Z0-9]/", "_", reset($imageNameAr));
				$ext       = end($imageNameAr);
				$imageName = $fileName . '.' . $ext;

				list($thisWidth, $thisHeight, $type, $attr) = getimagesize($pic->tempName);
				$imageTool = Yii::app()->imagemod->load($pic->tempName);

				if ($thisHeight > $thisWidth) { //verticle image
					if ($thisHeight > $newResizeHeight) {
						$imageTool->image_resize  = true;
						$imageTool->image_ratio_x = true;
						$imageTool->image_y       = $newResizeHeight;
					}
				} else { //horizontal image
					if ($thisWidth > $newResizeWidth) {
						$imageTool->image_resize  = true;
						$imageTool->image_ratio_y = true;
						$imageTool->image_x       = $newResizeWidth;
					}
				}
				$imageTool->file_new_name_body = $fileName;
				$imageTool->file_new_name_ext  = $ext;

				$imageTool->process($imagePath);

				if ($imageTool->processed) {
					$img             = new $imageModel();
					$img->recordId   = $id;
					$img->recordType = $recordType;
					$img->realName   = $pic->name;
					$img->name       = $imageName;
					$img->mimeType   = $pic->type;
					$img->fullPath   = realpath($imagePath);
					$img->save();
					$ids[$img->id] = $img->id;
				}
			}
		}
		return $ids;
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{

		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$delete = $this->loadModel($id)->delete();
			if ($delete) {
				//delete all images
				$galleryImages = GalleryImage::model()->findAllByAttributes([
																			'recordId'   => $id,
																			'recordType' => GalleryImage::RECORD_TYPE
																			]);
				foreach ($galleryImages as $galleryImage) {
					$galleryImage->delete();
				}
				$mainGalleryImages = MainGalleryImage::model()->findAllByAttributes([
																					'recordId'   => $id,
																					'recordType' => MainGalleryImage::RECORD_TYPE
																					]);
				foreach ($mainGalleryImages as $mainGalleryImage) {
					$mainGalleryImage->delete();
				}
				$mainViewImages = MainViewImage::model()->findAllByAttributes([
																			  'recordId'   => $id,
																			  'recordType' => MainViewImage::RECORD_TYPE
																			  ]);
				foreach ($mainViewImages as $mainViewImage) {
					$mainViewImage->delete();
				}
			}
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
			}
		} else {
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{

		/* Delete Empty Place */
		$placeInfo = Place::model()->findAll('createdByUserId=' . Yii::app()->user->getId() . ' AND title is NULL');
		if (count($placeInfo) > 0) {
			foreach ($placeInfo as $place) {
				Place::model()->findByPk($place->id)->delete();
			}
		}
		/* Delete Empty Place */

		$model        = new Place('search');
		$dataProvider = new CActiveDataProvider('Place',
												array(
													 'pagination' => array('pageSize' => 18),
													 'sort'       => array(
														 'attributes' => ['location.postcode' => 'addressId'],
													 ),
													 //													 'criteria'   => array('scopes' => array('onlyActive')),
												));

		if (empty($_GET['resetFilter_place_index_place-filter-form'])) {
			$model->statusId = [1, 2, 3, 4];
			$model->typeId   = [1, 2, 3];
		}

		$this->render('index', array(
									'dataProvider' => $dataProvider,
									'model'        => $model
							   ));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{

		$model = Place::model()->findByPk($id);
		if ($model === null) {
			throw new CHttpException(404, 'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{

		if (isset($_POST['ajax']) && $_POST['ajax'] === 'place-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionSelectImage($recordType, $recordId)
	{

		$this->layout = '';
		$model        = new Place();
		$browseImages = GalleryImage::model()->findAll("recordId='" . $recordId . "' AND recordType='" . $recordType . "'");
		$this->render('browseImages', array(
										   'browseImages'    => $browseImages,
										   'CKEditorFuncNum' => $_GET['CKEditorFuncNum'],
										   'model'           => $model,
									  ));
	}

	public function actionAddToEditor()
	{

		if (isset($_GET['chooseImage']) && !empty($_GET['chooseImage'])) {
			$selectedImageInfo = GalleryImage::model()->findByPk($_GET['chooseImage']);
			$fullImagePath     = Yii::app()->params['imgUrl'] . '/' . $selectedImageInfo->recordType . '/' . $selectedImageInfo->recordId . "/" . $selectedImageInfo->mediumName;
			echo '
				<script type="text/javascript">
				window.close();
				window.opener.CKEDITOR.tools.callFunction("' . $_GET['CKEditorFuncNum'] . '", "' . $fullImagePath . '");
				</script>
			';
		} else {
			echo 'Error!! Please go <a href="#" onclick="history.back()">back</a> and Choose Image.';
		}
	}

	public function actionAddCaption($id)
	{

		$captionVal = $_GET['captionVal'];
		File::model()->updateByPk($id, array('caption' => $captionVal));
		echo json_encode(array("result" => true));
	}
}