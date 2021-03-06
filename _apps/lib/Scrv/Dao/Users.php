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
		if ( ! $this->_Dao->connect(self::$_common_ini["db"]) ) {
			echo $this->_Dao->getErrorMessage();
			exit;
		}
		return true;
	}

	/**
	 * twitter_user_id による users テーブル検索
	 * @param string $twitter_user_id
	 * @return resultSet
	 */
	public function viewByTwitterUserId($twitter_user_id)
	{
		$result = getResultSet();
		try{
			$data = $this->_Dao->select(
				"SELECT * FROM users WHERE twitter_user_id=:twitter_user_id",
				array("twitter_user_id" => $twitter_user_id)
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
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
				$syncs_column = "t6.sync_point";
				$syncs_sql = "LEFT JOIN syncs t6 ON(t1.id=t6.user_id AND t6.user_com_id=:login_user_id) ";
				$params["login_user_id"] = $login_user_id;
			}
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.favtracks_count
				,t3.favalbums_count
				,t4.reviews_count
				,t5.favreviews_count
				,t7.username AS has_invited_username, t7.img_file AS has_invited_img_file
				,{$syncs_column}
				FROM users t1
				LEFT JOIN(SELECT user_id,count(id) AS favtracks_count  FROM favtracks  GROUP BY user_id)t2 ON(t1.id=t2.user_id)
				LEFT JOIN(SELECT user_id,count(id) AS favalbums_count  FROM favalbums  GROUP BY user_id)t3 ON(t1.id=t3.user_id)
				LEFT JOIN(SELECT user_id,count(id) AS reviews_count    FROM reviews    GROUP BY user_id)t4 ON(t1.id=t4.user_id)
				LEFT JOIN(SELECT user_id,count(id) AS favreviews_count FROM favreviews GROUP BY user_id)t5 ON(t1.id=t5.user_id)
				LEFT JOIN users t7 ON(t7.id=t1.has_invited_user_id)
				{$syncs_sql}
				WHERE t1.id=:user_id",
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
	public function lists($offset, $limit, $login_user_id = null, $sort="review_count", $order="desc")
	{
		$result = getResultSet();
		try{
			$syncs_column = "0 as sync_point";
			$syncs_sql = "";
			$params = array();
			if (isset( $login_user_id )) {
				$syncs_column = "t2.sync_point";
				$syncs_sql = "LEFT JOIN syncs t2 ON(t1.id=t2.user_id AND t2.user_com_id=:login_user_id) ";
				$params = array("login_user_id" => $login_user_id);
			}
			$data = $this->_Dao->select("
				SELECT
					t1.*, {$syncs_column}, count(t3.id) AS review_count,
					t4.username AS has_invited_username, t4.img_file AS has_invited_img_file
				FROM users t1
				$syncs_sql
				LEFT JOIN reviews t3 ON(t1.id=t3.user_id)
				LEFT JOIN users t4 ON(t4.id=t1.has_invited_user_id)
				GROUP BY t1.id ORDER BY {$sort} {$order}, t1.created LIMIT {$offset},{$limit}",
				$params
			);
			$data_count = $this->_Dao->select("SELECT count(id) as cnt FROM users");
			$result["status"] = true;
			$result["data"] = array(
				"lists" => $data,
				"lists_count" => $data_count[0]["cnt"],
			);
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
	public function addNew( $username, $password, $role, $has_invited_user_id=null )
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
				self::$_common_ini["password"]["hash_seed"],
				(int)self::$_common_ini["password"]["hash_count"]
			);
			// 登録
			$this->_Dao->insert(
				 "INSERT INTO users (username,password,role,has_invited_user_id,created,modified) "
				."VALUES(:username,:password_hash,:role,:has_invited_user_id,now(),now())",
				array(
					"username" => $username,
					"password_hash" => $password_hash,
					"role" => $role,
					"has_invited_user_id" => $has_invited_user_id,
				)
			);
			// 登録したユーザ情報を返す
			$add_user_data = $this->_Dao->select($user_sql, $user_params);
			$result["status"] = true;
			$result["data"] = $add_user_data[0];
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
	 * @param string $profile
	 * @param string $img_file default null
	 * @return resultSet
	 */
	public function save($user_id, $profile, $img_file=null)
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
			// img_fileはnullじゃない場合に設定
			$sql = "UPDATE users ";
			$set = "SET profile=:pfl, modified=now() ";
			$where = "WHERE id=:uid";
			$params = array(
				"pfl" => $profile,
				"uid" => $user_id,
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

	/**
	 * save username
	 * @param int $user_id
	 * @param string $org_username
	 * @param string $new_username
	 * @param string $current_password
	 * @param boolean $is_only_twitter_login default false
	 * @return string
	 * @throws \Exception
	 */
	public function saveUsername($user_id, $org_username, $new_username, $current_password, $is_only_twitter_login = false)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// 自分のuser_id以外で$usernameが存在するか
			$is_user_exists = $this->_Dao->select(
				"SELECT * FROM users WHERE username=:uname AND id<>:uid",
				array("uname" => $new_username, "uid" => $user_id)
			);
			if ( count($is_user_exists) > 0 ) {
				throw new \Exception("{$new_username} は既に存在します。");
			}

			// パスワードが正しいかチェック
			// twitter連携のみの場合は未チェック
			$password_hash = null;
			if (!$is_only_twitter_login) {
				$Password = new Password();
				$current_password_hash = $Password->makePasswordHash(
					$org_username,
					$current_password,
					self::$_common_ini["password"]["hash_seed"],
					(int)self::$_common_ini["password"]["hash_count"]
				);
				$user_result = $this->_Dao->select(
					"SELECT * FROM users WHERE id=:uid AND password=:pwd",
					array("uid"=>$user_id,"pwd" => $current_password_hash)
				);
				if ( count($user_result) === 0 ) {
					throw new \Exception("パスワードが一致しません。");
				}
				$password_hash = $Password->makePasswordHash(
					$new_username,
					$current_password,
					self::$_common_ini["password"]["hash_seed"],
					(int)self::$_common_ini["password"]["hash_count"]
				);
			}
			// 更新処理
			$row_count = $this->_Dao->update(
				"UPDATE users SET username=:uname, password=:pwd, modified=now() WHERE id=:uid",
				array(
					"uname" => $new_username,
					"pwd" => $password_hash,
					"uid" => $user_id,
				)
			);
			$result["status"] = true;
			$result["data"]["row_count"] = $row_count;
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
	 * save password
	 * @param type $user_id
	 * @param type $password
	 * @return string
	 * @throws \Exception
	 */
	public function savePassword($user_id, $username, $current_password, $password)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// パスワードチェック
			$Password = new Password();
			$current_password_hash = $Password->makePasswordHash(
				$username,
				$current_password,
				self::$_common_ini["password"]["hash_seed"],
				(int)self::$_common_ini["password"]["hash_count"]
			);

			// 存在チェック user_id, current_password
			$user_result = $this->_Dao->select("SELECT * FROM users WHERE id=:uid AND password=:pwd",array("uid"=>$user_id,"pwd" => $current_password_hash));
			if ( count($user_result) === 0 ) {
				throw new \Exception("現在のパスワードが一致しません。");
			}

			// 更新処理
			$password_hash = $Password->makePasswordHash(
				$username,
				$password,
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
	 * save twitter
	 * @param int $user_id
	 * @param string $twitter_user_id
	 * @param string $oauth_token
	 * @param string $oauth_token_secret
	 * @return string
	 */
	public function saveTwitter($user_id, $twitter_user_id, $oauth_token, $oauth_token_secret )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			$row_count = $this->_Dao->update("
				UPDATE users
				SET twitter_user_id=:tuid, twitter_user_token=:ot,twitter_user_secret=:ots,modified=now()
				WHERE id=:uid",
				array(
					"uid" => $user_id,
					"tuid" => $twitter_user_id,
					"ot" => $oauth_token,
					"ots" => $oauth_token_secret,
				)
			);

			// 最新のユーザデータを取得して返す
			$current_user_data = $this->_Dao->select(
				"SELECT * FROM users WHERE id=:uid",
				array("uid" => $user_id,)
			);

			$result["status"] = true;
			$result["data"]["user_data"] = $current_user_data[0];
			$this->_Dao->commit();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

	/**
	 * clear twitter
	 * @param int $user_id
	 * @return resultSet
	 */
	public function clearTwitter($user_id)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			$row_count = $this->_Dao->update("
				UPDATE users
				SET
					is_twitter_login=0,
					twitter_user_id=null,
					twitter_user_token=null,
					twitter_user_secret=null,
					modified=now()
				WHERE id=:uid",
				array("uid" => $user_id,)
			);
			// 最新のユーザデータを取得して返す
			$current_user_data = $this->_Dao->select(
				"SELECT * FROM users WHERE id=:uid",
				array("uid" => $user_id,)
			);
			$result["status"] = true;
			$result["data"]["user_data"] = $current_user_data[0];
			$this->_Dao->commit();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

}
