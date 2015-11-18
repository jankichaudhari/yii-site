<?php
/**
 * Migration converts all data in a table from latin1 to propper utf-8
 * mainly used to fix problems with £ symbol
 *
 * @author Vitaly Suhanov
 *
 */
class MigrationConvertToUTF8 extends CDbMigration
{
	protected $table = null;
	protected $pk = null;

	public function up()
	{

		if (!$this->table) {
			throw new Exception("migrations table is not defined");
		}
		if (!$this->pk) {
			throw new Exception("migrations tables primary key is not defined");
		}

		$this->dbConnection->createCommand("SET NAMES latin1")->execute();
		$data     = $this->dbConnection->createCommand("SELECT * FROM " . $this->table)->queryAll();
		$firstRow = reset($data);
		unset($firstRow[$this->pk]);
		$sql = [];
		foreach ($firstRow as $key => $value) {
			$sql[] = $key . " = :" . $key;
		}

		$command = $this->dbConnection->createCommand("UPDATE " . $this->table . " SET " . implode(", ", $sql) . " WHERE " . $this->pk . " = :" . $this->pk);
		$this->dbConnection->createCommand("SET NAMES utf8")->execute();
		foreach ($data as $value) {
			$command->execute($value);
		}
	}

	public function down()
	{

		echo  get_class($this) . " migration down does not affect any data.\n";
		return true;
	}

}