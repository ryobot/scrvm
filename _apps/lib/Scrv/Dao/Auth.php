<?php
/**
 * lib/Scrv/Dao/Auth.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;
use lib\Util\Password as Password;

/**
 * Auth class
 * @author tomita
 */
class Auth extends Dao
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
	 * login 処理
	 * @param string $title
	 * @param string $body
	 * @param integer $user_id
	 * @param integer $album_id
	 * @return resultSet
	 */
	public function login( $username, $password )
	{
		$result = getResultSet();
		try{
			$Password = new Password();
			$password_hash = $Password->makePasswordHash(
				$username,
				$password,
				self::$_common_ini["password"]["hash_seed"],
				(int)self::$_common_ini["password"]["hash_count"]
			);
			$user_result = $this->_Dao->select(
				"SELECT * FROM users WHERE username=:username AND password=:password_hash",
				array("username" => $username,"password_hash" => $password_hash,)
			);
			if ( count($user_result) === 0 ) {
				throw new \Exception("username または password が間違っています。");
			}
			$result["status"] = true;
			$result["data"] = $user_result[0];
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * twitter 新規ログイン時
	 * @param array $twitter_access_token
	 * @param integer $invited_user_id 招待者のuser_id
	 * @return resultSet
	 * @throws \Exception
	 */
	public function loginByTwitterNew(array $twitter_access_token, $invited_user_id = null)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{

			// 現在のユーザ数 >= 最大ユーザ人数 の場合新規登録不可
			$user_count_result = $this->_Dao->select(
				"SELECT count(*) as user_count from users"
			);
			if ( $user_count_result[0]["user_count"] >= (int)self::$_common_ini["invites"]["max_user_count"] ) {
				throw new \Exception("現在、新規新規ユーザ登録はできません。");
			}

			$Password = new Password();
			$random_hash = $Password->makeRandomHash($twitter_user_id . $twitter_user_token);

			// username を作成(ランダム文字を含める)
			$twitter_user_screen_name = $twitter_access_token["screen_name"];
			$twitter_user_id = $twitter_access_token["user_id"];
			$twitter_user_token = $twitter_access_token["oauth_token"];
			$twitter_user_secret = $twitter_access_token["oauth_token_secret"];
			$username = "{$twitter_user_screen_name}-{$twitter_user_id}";

			// 存在チェック
			$user_sql = "SELECT * FROM users WHERE username=:username";
			$user_params = array("username"=>$username,);
			$user_result = $this->_Dao->select($user_sql, $user_params);
			if ( count($user_result) > 0 ) {
				throw new \Exception("その username は既に登録されています。");
			}

			// 登録
			$this->_Dao->insert("
				INSERT INTO users
				(
					username,role,
					has_invited_user_id,
					is_twitter_login, twitter_user_id, twitter_user_token, twitter_user_secret,
					created,modified
				)
				VALUES(
					:username,'author',
					:invited_user_id,
					1,:twitter_user_id, :twitter_user_token, :twitter_user_secret,
					now(),now())
				",
				array(
					"username" => $username,
					"invited_user_id" => $invited_user_id,
					"twitter_user_id" => $twitter_user_id,
					"twitter_user_token" => $twitter_user_token,
					"twitter_user_secret" => $twitter_user_secret,
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
	 * twitter ログイン時
	 * @param array $twitter_access_token
	 * @return string
	 * @throws \Exception
	 */
	public function loginByTwitter(array $twitter_access_token)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			$twitter_user_id = $twitter_access_token["user_id"];
			$twitter_user_token = $twitter_access_token["oauth_token"];
			$twitter_user_secret = $twitter_access_token["oauth_token_secret"];

			// 存在チェック
			$user_sql = "SELECT * FROM users WHERE twitter_user_id=:twitter_user_id";
			$user_params = array("twitter_user_id"=>$twitter_user_id,);
			$user_result = $this->_Dao->select($user_sql, $user_params);
			if ( count($user_result) === 0 ) {
				throw new \Exception("そのユーザは存在しません。");
			}

			// update
			$this->_Dao->update("
				UPDATE users
				SET
					is_twitter_login = 1,
					twitter_user_token = :twitter_user_token,
					twitter_user_secret = :twitter_user_secret,
					modified = now()
				WHERE twitter_user_id=:twitter_user_id
				",
				array(
					"twitter_user_id" => $twitter_user_id,
					"twitter_user_token" => $twitter_user_token,
					"twitter_user_secret" => $twitter_user_secret,
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

}
