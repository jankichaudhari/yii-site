<?php
class FileController extends AdminController
{
	public $layout = '//layouts/adminDefault';

	public function actionEdit()
	{

		$this->render('edit');
	}

	public function actionPageGalleryImage($recordType = 'ContactPageGallery', $recordId = 1)
	{

		$title     = 'Contact page gallery';
		$modelName = 'PageGalleryImage';
		$criteria  = new CDbCriteria();
		$criteria->compare('recordId', $recordId, false, 'AND', false);
		$criteria->compare('recordType', $recordType, false, 'AND', false);
		$criteria->order = "displayOrder ASC";
		$galleryImages   = PageGalleryImage::model()->findAll($criteria);
		if (isset($_POST) && $_POST) {
			$uploadedFiles = CUploadedFile::getInstancesByName($modelName);
			if ($uploadedFiles) {
				foreach ($uploadedFiles as $fileKey => $fileVal) {
					$file             = new $modelName();
					$file->file       = $fileVal;
					$file->recordId   = $recordId;
					$file->recordType = $recordType;

					$file->save();
				}

				$this->redirect('PageGalleryImage');
			}

		}
		$this->render('_pageGalleryImage', [
										   'galleryImages' => $galleryImages,
										   'title'         => $title,
										   'recordType'    => $recordType,
										   'recordId'      => $recordId
										   ]);
	}

	public function actionUpload($recordType, $id, $InstanceName, $fileType = "File", $filePath = "", $fileExtName = "", $newResizeWidth = 1600, $newResizeHeight = 1280)
	{

		if (isset($recordType) && isset($id)) {
			$uploadedFiles = CUploadedFile::getInstancesByName($InstanceName);
			$modelName     = $fileType;
			if (empty($filePath)) {
				$fileParam = ($fileType == 'File') ? 'filePath' : 'imgPath';
				$filePath  = Yii::app()->params[$fileParam] . '/' . $recordType . '/' . $id . '/';
			}

			Yii::app()->file->set($filePath)->createDir(0777);
			$uploadedFilesIds = array();
			if ($uploadedFiles) {
				foreach ($uploadedFiles as $fileKey => $fileVal) {
					$fileName = str_replace(array(" ", "-"), "_", $fileVal->name);

					list($baseName, $ext) = explode(".", $fileName);
					$fileName = $baseName . "_" . $fileExtName . "." . $ext;

					list($thisWidth, $thisHeight, $type, $attr) = getimagesize($fileVal->tempName);
					$imageTool = Yii::app()->imagemod->load($fileVal->tempName);

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
					$imageTool->file_new_name_body = $baseName . "_" . $fileExtName;
					$imageTool->file_new_name_ext  = $ext;

					$imageTool->process($filePath);

					if ($imageTool->processed) {
						$file             = new $modelName();
						$file->recordId   = $id;
						$file->recordType = $recordType;
						$file->realName   = $fileVal->name;
						$file->name       = $fileName;
						$file->mimeType   = $fileVal->type;
						$file->fullPath   = realpath($filePath);
						$file->save(false);
						$uploadedFilesIds[$file->id] = $file->id;
					}
				}
			}

			return $uploadedFilesIds;
		} else {
			return false;
		}
	}

	public function actionRearrange()
	{

		$updateOrderIds = '';
		if (isset($_POST['updateOrderIds'])) {
			$updateOrderIds = $_POST['updateOrderIds'];
		}
		if (isset($_POST['recordId']) && isset($_POST['recordType'])) {
			$recordId   = $_POST['recordId'];
			$recordType = $_POST['recordType'];
			File::model()->rearrange($updateOrderIds, $recordId, $recordType);
			echo 'done';
		} else {
			echo 'error';
		}

	}

	public function actionDelete($id, $fileModel, $recordModel, $multipleImages = 'no')
	{

		switch ($fileModel) {
			case 'GalleryImage' :
				$modelName = '\application\models\Place\GalleryImage';
				break;
			case 'MainGalleryImage' :
				$modelName = '\application\models\Place\MainGalleryImage';
				$thisImage = File::model()->findByPk($id);
				$recordModel::model()->updateByPk($thisImage->recordId, array('mainGalleryImageId' => '0'));
				break;
			case 'MainViewImage' :
				$modelName     = '\application\models\Place\MainViewImage';
				$thisViewImage = File::model()->findByPk($id);
				$recordModel::model()->updateByPk($thisViewImage->recordId, array('mainViewImageId' => '0'));
				break;
			default :
				$modelName = $fileModel;
		}
		$modelName = class_exists($modelName) ? $modelName : 'File';
		if ($modelName::model()->findByPk($id)->delete()) {
			if ($multipleImages == 'yes') {
				echo json_encode(array("result" => false));
			} else {
				echo json_encode(array("result" => true));
			}
		} else {
			echo json_encode(array("result" => false));
		}
	}
}