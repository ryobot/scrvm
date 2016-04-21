<?php
/**
 * lib/Scrv/Dao/Tags.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Tags class
 * @author mgng
 */
class Tags extends Dao
{
	/**
	 * Dao object
	 * @var Dao
	 */
	private $_Dao = null;

	/**
	 * construct
	 * @return boolean
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_Dao = new Dao();
		if ( ! $this->_Dao->connect($this->_common_ini["db"]) ) {
			echo $this->_Dao->getErrorMessage();
			exit;
		}
		return true;
	}


	public function add($album_id, $create_user_id, $tag)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// 同一alubum_idで同じtagはダメ
			$user_result = $this->_Dao->select(
				"SELECT * FROM tags WHERE album_id=:album_id AND tag=:tag",
				array("album_id"=>$album_id,"tag"=>$tag,)
			);
			if ( count($user_result) > 0 ) {
				throw new \Exception("[{$tag}]は既に登録されています。");
			}

			$this->_Dao->insert("
				INSERT INTO tags(album_id, create_user_id, tag, can_be_deleted, created)
				VALUES (:album_id,:cuid,:tag,1,now())",
				array("album_id" => $album_id, "cuid" => $create_user_id, "tag" => $tag,)
			);

			$result["status"] = true;
			$this->_Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$this->_Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

}
