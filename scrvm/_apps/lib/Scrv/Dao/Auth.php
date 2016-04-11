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
 * @author mgng
 */
class Auth extends Dao
{
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
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}

		try{
			// 存在チェック、パスワードは共通ハッシュ化
			$Password = new Password();
			$password_hash = $Password->makePasswordHash(
				$username,
				$password,
				$this->_common_ini["password"]["hash_seed"],
				(int)$this->_common_ini["password"]["hash_count"]
			);
			$user_result = $Dao->select(
				"SELECT * FROM users WHERE username=:username AND password=:password_hash",
				array(
					"username" => $username,
					"password_hash" => $password_hash,
				)
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

}
