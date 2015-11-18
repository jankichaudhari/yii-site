<?php

class m130919_161131_fix_older_dea_descriptions extends CDbMigration
{
	public function up()
	{
		$options = ['allowedTags' => ['p', 'b', 'i', 'ul', 'li', 'ol', 'a', 'strong', 'em', 'img'], 'allowedAttributes' => ['class', 'href', 'src']];

		$func = function ($str, $options) {
			include_once 'Zend/Filter/StripTags.php';
			$allowedAttributes = (isset($options['allowedAttributes']) ? $options['allowedAttributes'] : []);
			$allowedTags       = (isset($options['allowedTags']) ? $options['allowedTags'] : []);
			$strip             = new Zend_Filter_StripTags($allowedTags, $allowedAttributes);
			return $strip->filter($str);
		};

		$data    = Yii::app()->db->createCommand("SELECT dea_id, dea_description FROM deal WHERE dea_description LIKE '%<div%'")->queryAll();
		$command = Yii::app()->db->createCommand("UPDATE deal SET dea_description = :description WHERE dea_id = :id");
		foreach ($data as $key => $value) {
			$command->execute(['id' => $value['dea_id'], 'description' => $func($value['dea_description'], $options)]);
		}
	}

	public function down()
	{
		echo "m130919_161131_fix_older_dea_descriptions does not support migration down.\n";
		return false;
	}
}