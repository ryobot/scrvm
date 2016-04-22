<?php
/**
 * lib/Scrv/Dao/Invites.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Invites class
 * @author tomita
 */
class Invites extends Dao
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

	/**
	 * create invite
	 * @param int $user_id
	 * @param string $hash
	 * @param string $role
	 * @return resultSet
	 */
	public function createInvite($user_id, $hash, $role)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{

			// ユーザ数が common.ini の上限を超えていたらNG
			$user_count = $this->_Dao->select("SELECT count(id) AS cnt FROM users");
			if ( $user_count[0]["cnt"] > (int)$this->_common_ini["invites"]["max_user_count"] ) {
				throw new \Exception("ユーザ数上限に達しているためリンクを生成できません。");
			}

			// roleがadmin以外で招待数条件を超えていたらNG
			$search_result = $this->_Dao->select("SELECT invited_count FROM users WHERE id=:uid",array("uid"=>$user_id,));
			if (count($search_result) !== 1) {
				throw new \Exception("データが見つかりません。");
			}
			if ( $role !== "admin"
				&& (int)$this->_common_ini["invites"]["max_invited_count"] <= $search_result[0]["invited_count"]) {
				throw new \Exception("招待数の上限を超えているためリンクを生成できません。");
			}

			$expire_time = $this->_nowTimestamp + (int)$this->_common_ini["invites"]["expire"];
			$exp = date("Y-m-d H:i:s", $expire_time);

			// 有効期限切れデータ削除
			$this->_Dao->delete("DELETE FROM invitations WHERE expire > :exp",array("exp"=>$exp,));

			// 登録
			$sql = "INSERT INTO invitations (user_id,hash,created,expire) VALUES(:uid,:hash,now(),:exp)";
			$params = array(
				"uid" => $user_id,
				"hash" => $hash,
				"exp" => $exp,
			);
			$this->_Dao->insert($sql, $params);

			// invited_count++
			$this->_Dao->update(
				"UPDATE users SET invited_count=invited_count+1 WHERE id=:uid",
				array("uid" => $user_id,)
			);

			// 更新後のユーザ情報を返す
			$current_user_data = $this->_Dao->select("SELECT * FROM users WHERE id=:uid",array("uid" => $user_id,));

			$result["status"] = true;
			$result["data"] = $current_user_data[0];
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

	/**
	 * find hash
	 * @param string $hash
	 * @return string
	 */
	public function findHash($hash)
	{
		$result = getResultSet();
		try{
			$expire_time = $this->_nowTimestamp + (int)$this->_common_ini["invites"]["expire"];
			$exp = date("Y-m-d H:i:s", $expire_time);
			$data = $this->_Dao->select(
				"SELECT * FROM invitations WHERE hash=:hash AND expire <= :exp",
				array("hash" => $hash,"exp" => $exp,)
			);
			if ( count($data) === 1 ) {
				$result["status"] = true;
				$result["data"] = $data[0];
			}
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * delete hash
	 * @param string $hash
	 * @return resultSet
	 */
	public function deleteHash($hash)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			$this->_Dao->delete("DELETE FROM invitations WHERE hash=:hash",array("hash"=>$hash,));
			$result["status"] = true;
			$this->_Dao->commit();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}



}
