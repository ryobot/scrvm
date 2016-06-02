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

}
