<?php
/**
 * lib/Scrv/Dao/Admin.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;
use lib\Util\Password as Password;

/**
 * Admin class
 * @author tomita
 */
class Admin extends Dao
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
		if ( ! $this->_Dao->connect(self::$_common_ini["db"]) ) {
			echo $this->_Dao->getErrorMessage();
			exit;
		}
		return true;
	}

	/**
	 * save password
	 * @param type $user_id
	 * @param type $password
	 * @return string
	 * @throws \Exception
	 */
	public function savePassword($user_id, $username, $current_password_hash, $password_new)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// 存在チェック user_id, current_password
			$user_result = $this->_Dao->select("SELECT * FROM users WHERE id=:uid AND password=:pwd",array("uid"=>$user_id,"pwd" => $current_password_hash));
			if ( count($user_result) === 0 ) {
				throw new \Exception("現在のパスワードが一致しません。");
			}
			// 更新処理
			$Password = new Password();
			$password_hash = $Password->makePasswordHash(
				$username,
				$password_new,
				self::$_common_ini["password"]["hash_seed"],
				(int)self::$_common_ini["password"]["hash_count"]
			);
			$sql = "UPDATE users SET password=:pwd,modified=now() WHERE id=:uid";
			$params = array(
				"pwd" => $password_hash,
				"uid" => $user_id,
			);
			$row_count = $this->_Dao->update($sql, $params);
			$result["status"] = true;
			$result["data"]["rowcount"] = $row_count;
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
	 * clear invited count
	 * @param int $user_id
	 * @param string $username
	 * @return string
	 * @throws \Exception
	 */
	public function clearInvitedCount($user_id, $username)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// 存在チェック user_id, username
			$user_result = $this->_Dao->select(
				"SELECT * FROM users WHERE id=:uid AND username=:uname",
				array("uid"=>$user_id,"uname" => $username)
			);
			if ( count($user_result) === 0 ) {
				throw new \Exception("ユーザが存在しません。");
			}
			// 更新処理
			$sql = "UPDATE users SET invited_count=0,modified=now() WHERE id=:uid";
			$params = array("uid" => $user_id,);
			$row_count = $this->_Dao->update($sql, $params);
			$result["status"] = true;
			$result["data"]["rowcount"] = $row_count;
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
