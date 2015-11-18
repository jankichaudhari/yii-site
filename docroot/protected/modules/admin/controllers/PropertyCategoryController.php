<?php

class PropertyCategoryController extends AdminController
{
	/** @var string */
	public $pathToImages;

	public function __construct($id, $module = null)
	{
		$this->pathToImages = Yii::app()->params['imgPath'] . "/propertyCategory";
		parent::__construct($id, $module);
	}

	/**
	 *
	 */
	public function actionIndex()
	{
		$model = new PropertyCategory('search');
		$this->render('index', compact('model'));
	}

	public function actionCreate()
	{
		$model = new PropertyCategory();
		if ($this->saveCategory($model)) {
			$this->redirect(['update', 'id' => $model->id]);
		}

		$this->render('form', ['model' => $model]);
	}

	/**
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionUpdate($id)
	{

		$model = PropertyCategory::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'The requested property category does not exist.');
		}

		if ($this->saveCategory($model)) {
			$this->redirect(['update', 'id' => $model->id]);
		}

		$this->render('form', ['model' => $model]);
	}

	/**
	 * @param PropertyCategory $model
	 * @return bool
	 */
	private function saveCategory(PropertyCategory $model)
	{
		$saved = false;
		if (isset($_POST['PropertyCategory']) && $_POST['PropertyCategory']) {
			$model->attributes = $_POST['PropertyCategory'];

			if ($saved = $model->save()) {
				Yii::app()->user->setFlash('success', 'Saved Successfully');
			}
		}
		return $saved;
	}

	public function actionManagePhotos($id)
	{
		$this->layout = '//layouts/new/main';

		if (!$id) {
			throw new CHttpException(404, 'The requested property category does not exist.');
		}

		$model = PropertyCategory::model()->findByPk($id);
		if (isset($_POST['upload-photo']) && $_POST['upload-photo']) {

			$type       = isset($_POST['category-photo-type']) ? $_POST['category-photo-type'] : '';
			$recordType = PropertyCategory::CATEGORY_PHOTO_PREFIX . $type;
			$this->saveImages($model, 'category-photo', $recordType);
			$this->redirect(['ManagePhotos', 'id' => $id]);
		}
		$this->render('managePhotos', ['model' => $model]);
	}

	/**
	 * What the hell is this method? it was discussed over 9000 times that such code
	 * should not be anywhere close to controller as it is pure model's logic.
	 *
	 * @param $model
	 * @param $instanceName
	 * @param $recordType
	 * @return array
	 */
	private function saveImages($model, $instanceName, $recordType)
	{
		/** @var $images CUploadedFile[] */
		$images          = CUploadedFile::getInstancesByName($instanceName);
		$imagePath       = $this->pathToImages . "/" . $model->id;
		$newResizeWidth  = 1920;
		$newResizeHeight = 1024;
		Yii::app()->file->set($imagePath)->createDir(0777);
		$ids = array();

		if ($images) {
			if (File::model()->findByAttributes(['recordId' => $model->id, 'recordType' => $recordType])) {
				File::model()->findByAttributes(['recordId' => $model->id, 'recordType' => $recordType])->delete();
			}
			foreach ($images as $num => $pic) {
				$imageNameAr = explode(".", $pic->name);
				$fileName    = $recordType;
				$ext         = end($imageNameAr);
				$imageName   = $fileName . '.' . $ext;

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
					$img             = new File();
					$img->recordId   = $model->id;
					$img->recordType = $recordType;
					$img->realName   = $pic->name;
					$img->name       = $imageName;
					$img->mimeType   = $pic->type;
					$img->fullPath   = realpath($imagePath);
					$img->save(false);
					$ids[$img->id] = $img->id;
				}
			}
		}
		return $ids;
	}
}