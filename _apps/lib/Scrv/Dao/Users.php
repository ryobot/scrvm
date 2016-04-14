<?php
/**
 * lib/Scrv/Dao/Users.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;
use lib\Util\Password as Password;

/**
 * Users class
 * @author tomita
 */
class Users extends Dao
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
	 * users view
	 * @param integer $user_id
	 * @param integer $login_user_id
	 * @return resultSet
	 */
	public function view($user_id, $login_user_id = null)
	{
		$result = getResultSet();
		try{
			$syncs_column = "0 as sync_point";
			$syncs_sql = "";
			$params = array("user_id" => $user_id);
			if (isset( $login_user_id )) {
				$syncs_column = "t5.sync_point";
				$syncs_sql = "LEFT JOIN syncs t5 ON(t1.id=t5.user_id AND t5.user_com_id=:login_user_id) ";
				$params["login_user_id"] = $login_user_id;
			}
			$data = $this->_Dao->select(
				 "SELECT t1.*, t2.favtracks_count, t3.favalbums_count, t4.reviews_count, {$syncs_column} "
				."FROM users t1 "
				."LEFT JOIN(SELECT user_id,count(id) AS favtracks_count FROM favtracks GROUP BY user_id)t2 ON(t1.id=t2.user_id) "
				."LEFT JOIN(SELECT user_id,count(id) AS favalbums_count FROM favalbums GROUP BY user_id)t3 ON(t1.id=t3.user_id) "
				."LEFT JOIN(SELECT user_id,count(id) AS reviews_count   FROM reviews   GROUP BY user_id)t4 ON(t1.id=t4.user_id) "
				.$syncs_sql
				."WHERE t1.id=:user_id",
				$params
			);
			$result["status"] = true;
			$result["data"] = $data[0];
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * users lists
	 * @param integer $offset
	 * @param integer $limit
	 * @param integer $login_user_id
	 * @return resultSet
	 */
	public function lists($offset, $limit, $login_user_id = null)
	{
		$result = getResultSet();
		try{
			$syncs_column = "0 as sync_point";
			$syncs_sql = "";
			$params = array();
			if (isset( $login_user_id )) {
				$syncs_column = "t2.sync_point";
				$syncs_sql = "LEFT JOIN syncs t2 ON(t1.id=t2.user_com_id AND t2.user_id=:login_user_id) ";
				$params = array("login_user_id" => $login_user_id);
			}
			$data = $this->_Dao->select(
				 "SELECT t1.*, {$syncs_column}, count(t3.id) AS review_count FROM users t1 "
				. $syncs_sql
				."LEFT JOIN reviews t3 ON(t1.id=t3.user_id) "
				."GROUP BY t1.id ORDER BY t1.created LIMIT {$offset},{$limit}",
				$params
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * addNew 処理
	 * @param string $username
	 * @param string $password
	 * @param string $role
	 * @return resultSet
	 */
	public function addNew( $username, $password, $role )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// 存在チェック
			$user_sql = "SELECT * FROM users WHERE username=:username";
			$user_params = array("username"=>$username,);
			$user_result = $this->_Dao->select($user_sql, $user_params);
			if ( count($user_result) > 0 ) {
				throw new \Exception("その username は既に登録されています。");
			}
			$Password = new Password();
			$password_hash = $Password->makePasswordHash(
				$username,
				$password,
				$this->_common_ini["password"]["hash_seed"],
				(int)$this->_common_ini["password"]["hash_count"]
			);
			// 登録
			$this->_Dao->insert(
				 "INSERT INTO users (username,password,role,created,modified) "
				."VALUES(:username,:password_hash,:role,now(),now())",
				array(
					"username" => $username,
					"password_hash" => $password_hash,
					"role" => $role,
				)
			);
			// 登録したユーザ情報を返す
			$add_user_data = $this->_Dao->select($user_sql, $user_params);
			$result["status"] = true;
			$result["data"] = array("user_data" => $add_user_data,);
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
	 * save 処理
	 * @param integer $user_id
	 * @param string $username
	 * @param string $password
	 * @param string $img_file default null
	 * @return resultSet
	 */
	public function save($user_id, $username, $password, $img_file=null)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// 存在チェック user_id
			$user_result = $this->_Dao->select(
				"SELECT * FROM users WHERE id=:user_id",
				array("user_id"=>$user_id,)
			);
			if ( count($user_result) === 0 ) {
				throw new \Exception("ユーザが存在しません。");
			}
			// 存在チェック user_name
			$username_result = $this->_Dao->select(
				"SELECT * FROM users WHERE username=:username AND id<>:user_id",
				array("username"=>$username,"user_id"=>$user_id,)
			);
			if ( count($username_result) > 0 ) {
				throw new \Exception("その username は既に登録されています。");
			}
			$Password = new Password();
			$password_hash = $Password->makePasswordHash(
				$username,
				$password,
				$this->_common_ini["password"]["hash_seed"],
				(int)$this->_common_ini["password"]["hash_count"]
			);
			// img_fileはnullじゃない場合に設定
			$sql = "UPDATE users ";
			$set = "SET username=:username,password=:password_hash,modified=now() ";
			$where = "WHERE id=:user_id";
			$params = array(
				"username" => $username,
				"password_hash" => $password_hash,
				"user_id" => $user_id,
			);
			if ( $img_file !== null ) {
				$set .= ",img_file=:img_file ";
				$params["img_file"] = $img_file;
			}
			$row_count = $this->_Dao->update("{$sql}{$set}{$where}", $params);
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
