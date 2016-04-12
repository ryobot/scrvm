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
	 * users view
	 * @param integer $user_id
	 * @return resultSet
	 */
	public function view($user_id)
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			$data = $Dao->select(
				 "SELECT t1.*, t2.favtracks_count, t3.favalbums_count, t4.reviews_count "
				."FROM users t1 "
				."LEFT JOIN (SELECT user_id, count(id) AS favtracks_count FROM favtracks GROUP BY user_id) t2 ON (t1.id=t2.user_id) "
				."LEFT JOIN (SELECT user_id, count(id) AS favalbums_count FROM favalbums GROUP BY user_id) t3 ON (t1.id=t3.user_id) "
				."LEFT JOIN (SELECT user_id, count(id) AS reviews_count   FROM reviews   GROUP BY user_id) t4 ON (t1.id=t4.user_id) "
				."WHERE t1.id=:user_id",
				array( "user_id" => $user_id, )
			);
			$result["status"] = true;
			$result["data"] = $data[0];
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * users lists
	 * @param integer $offset
	 * @param integer $limit
	 * @return resultSet
	 */
	public function lists($offset, $limit)
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			$data = $Dao->select(
				 "SELECT t1.*, count(t2.id) as review_count FROM users t1 "
				."LEFT JOIN reviews t2 ON ( t1.id=t2.user_id ) "
				."GROUP BY t1.id ORDER BY t1.created LIMIT {$offset},{$limit}",
				array()
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
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
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}

		$Dao->beginTransaction();
		try{
			// 存在チェック
			$Password = new Password();
			$user_result = $Dao->select( "SELECT * FROM users WHERE username=:username", array(
				"username" => $username,
			));
			if ( count($user_result) > 0 ) {
				throw new \Exception("その username は既に登録されています。");
			}

			// 登録処理パスワードは共通ハッシュ化
			$password_hash = $Password->makePasswordHash(
				$username,
				$password,
				$this->_common_ini["password"]["hash_seed"],
				(int)$this->_common_ini["password"]["hash_count"]
			);
			$insert_row_count = $Dao->insert(
				 "INSERT INTO users (username,password,role,created,modified) "
				."VALUES(:username,:password_hash,:role,now(),now())",
				array(
					"username" => $username,
					"password_hash" => $password_hash,
					"role" => $role,
				)
			);
			$result["status"] = true;
			$result["data"]["rowcount"] = $insert_row_count;
			$Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = "exp error - " . $ex->getMessage();
			$Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$Dao->rollBack();
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
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}

		$Dao->beginTransaction();
		try{
			// 存在チェック user_id
			$user_result = $Dao->select(
				"SELECT * FROM users WHERE id=:user_id",
				array("user_id" => $user_id,)
			);
			if ( count($user_result) === 0 ) {
				throw new \Exception("ユーザが存在しません。");
			}

			// 存在チェック user_name
			$username_result = $Dao->select(
				"SELECT * FROM users WHERE username=:username AND id<>:user_id",
				array(
					"username" => $username,
					"user_id" => $user_id,
				)
			);
			if ( count($username_result) > 0 ) {
				throw new \Exception("その username は既に登録されています。");
			}

			// 登録処理パスワードは共通ハッシュ化
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
			$row_count = $Dao->update("{$sql}{$set}{$where}", $params);
			$result["status"] = true;
			$result["data"]["rowcount"] = $row_count;
			$Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$Dao->rollBack();
		}
		return $result;
	}

}
