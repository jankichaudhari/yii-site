<?php

class m130410_154122_duplicate_max_and_thumb_property_images_and_rename extends CDbMigration
{
	public function up()
	{
		$folderPath = Yii::app()->params['imgPath'] . '/property/p/';
		$sql = "SELECT * FROM media WHERE med_table = 'deal'";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		foreach($records as $record){
			$fullName = explode(".",$record['med_file']);
			$name = reset($fullName);
			$ext = end($fullName);
			$newFolderPath = $folderPath . $record['med_row'];
			$oldThumbName = realpath($newFolderPath) . "/".$name . "_s" .  "." . $ext;
			$newThumbName = realpath($newFolderPath) . "/".$name . Media::SUFFIX_THUMB1 .  "." . $ext;
			$newFullName = realpath($newFolderPath) . "/".$name . Media::SUFFIX_ORIGINAL .  "." . $ext;
			if ($images = glob(realpath($newFolderPath) . "/".$name."*.{jpg,gif,png,jpg,JPG,GIF,PNG,JPEG}", GLOB_BRACE)) {
				$max = 0;
				$cnt = 0;
				$maxImg = '';
				foreach ($images as $image) {
					$cnt ++;
					$size = filesize($image);
					if($size > $max){
						$max = $size;
						$maxImg = $image;
					}
					if(!file_exists($newThumbName) && ($image == $oldThumbName)){
						if(copy($image,$newThumbName)){
//							echo "Thumbnail Added : " . $newThumbName . "<br>";
						}
					}
				}
				if(!file_exists($newFullName) && copy($maxImg, $newFullName)){
//					echo "Added : " . $newFullName ."<br>";
				}
			}
		}
	}

	public function down()
	{
		return true;
	}
}