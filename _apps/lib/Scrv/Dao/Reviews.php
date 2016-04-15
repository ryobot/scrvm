<?php
/**
 * lib/Scrv/Dao/Reviews.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;
use lib\Util\Syncs as Syncs;

/**
 * Reviews class
 * @author tomita
 */
class Reviews extends Dao
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
	 * review
	 * @param int $review_id
	 * @return resultSet
	 */
	public function review( $review_id )
	{
		$result = getResultSet();
		try{
			$data = $this->_Dao->select("SELECT * FROM reviews WHERE id=:review_id",array("review_id" => $review_id,));
			if ( count($data) !== 1 ) {
				throw new \Exception("not found");
			}
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
	 * lists
	 * @param int $offset
	 * @param int $limit
	 * @return resultSet
	 */
	public function lists( $offset, $limit )
	{
		$result = getResultSet();
		try{
			$data = $this->_Dao->select(
				"SELECT "
				."t1.*, t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks,"
				."t3.username,t3.img_file as user_img_file "
				."FROM reviews t1 "
				."INNER JOIN albums t2 ON (t1.album_id=t2.id) "
				."INNER JOIN users t3 ON (t1.user_id=t3.id) "
				."ORDER BY t1.created DESC LIMIT {$offset},{$limit}"
			);
			$data_count = $this->_Dao->select("SELECT count(id) cnt FROM reviews");
			$result["status"] = true;
			$result["data"] = array(
				"reviews" => $data,
				"reviews_count" => $data_count[0]["cnt"],
			);
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * view
	 * @param integer $user_id
	 * @param integer $offset
	 * @param integer $limit
	 * @return resultSet
	 */
	public function view( $user_id, $offset, $limit )
	{
		$result = getResultSet();
		try{
			$data = $this->_Dao->select(
				 "SELECT t1.*, t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks,"
				."t3.username,t3.img_file as user_img_file "
				."FROM reviews t1 "
				."INNER JOIN albums t2 ON (t1.album_id=t2.id) "
				."INNER JOIN users t3 ON (t1.user_id=t3.id) "
				."WHERE user_id=:user_id "
				."ORDER BY t1.created DESC LIMIT {$offset},{$limit}",
				array("user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * add
	 * @param int $user_id
	 * @param int $album_id
	 * @param string $listening_last
	 * @param string $listening_system
	 * @param string $body
	 * @return resultSet
	 * @throws \Exception
	 */
	public function add( $user_id, $album_id, $listening_last, $listening_system, $body )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// アルバムが存在するかチェック
			$album_result = $this->_Dao->select("SELECT * FROM albums WHERE id=:album_id",array("album_id" => $album_id,));
			if ( count($album_result) !== 1 ) {
				throw new \Exception("not found.");
			}
			// 登録
			$row_count = $this->_Dao->insert(
				 "INSERT INTO reviews (album_id,user_id,body,listening_last,listening_system,created) "
				."VALUES(:album_id,:user_id,:body,:listening_last,:listening_system,:now)",
				array(
					"album_id" => $album_id,
					"user_id" => $user_id,
					"body" => $body,
					"listening_last" => $listening_last,
					"listening_system" => $listening_system,
					"now" => date("Y-m-d H:i:s", $this->_nowTimestamp),
				)
			);

			// XXX レビュー加算処理…
			$now_date_str = date("Y-m-d H:i:s", $this->_nowTimestamp);
			$reviews_result = $this->_Dao->select(
				"SELECT * FROM reviews WHERE album_id=:aid AND user_id<>:uid",
				array("aid"=>$album_id,"uid"=>$user_id,)
			);
			if ( count($reviews_result) === 0 ) {
			} else {
				$Syncs = new Syncs();
				foreach($reviews_result as $review){
					$syncs_calc = $Syncs->calcPoint($now_date_str, $review["created"]);
					$add_point = $syncs_calc["point"] < 0 ? 0 : $syncs_calc["point"];
					$syncs_params = array(
						"id1"=>$user_id,
						"id2"=>$review["user_id"],
						"id3"=>$user_id,
						"id4"=>$review["user_id"],
					);
					$sync_list = $this->_Dao->select(
						"SELECT id,sync_point FROM syncs WHERE user_id IN(:id1,:id2) AND user_com_id IN(:id3,:id4)",
						$syncs_params
					);
					if ( count($sync_list) === 0 ){
						// 2行insert
						$this->_Dao->insert(
							 "INSERT INTO syncs (user_id,user_com_id,sync_point) "
							."values(:id1,:id2,{$add_point}),(:id4,:id3,{$add_point})",
							$syncs_params
						);
					} else {
						// 加算してupdate,加算した数値が0未満の場合は0に丸める
						foreach($sync_list as $sync) {
							$add_sync_point = $sync["sync_point"] + $add_point;
							$this->_Dao->update(
								"UPDATE syncs SET sync_point=:add_sync_point WHERE id=:id",
								array(
									"add_sync_point" => $add_sync_point < 0 ? 0 : $add_sync_point,
									"id" => $sync["id"],
								)
							);
						}
					}
				}
			}

			$result["status"] = true;
			$result["data"] = array(
				"row_count" => $row_count,
				"album_data" => $album_result[0],
			);
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
	 * delete
	 * @param int $user_id
	 * @param int $review_id
	 * @return resultSet
	 * @throws \Exception
	 */
	public function del( $user_id, $review_id )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// reviewが存在するかチェック
			$review_result = $this->_Dao->select(
				"SELECT * FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
			if ( count($review_result) !== 1 ) {
				throw new \Exception("not found.");
			}

			// XXX レビュー減算処理…
			$now_date_str = date("Y-m-d H:i:s", $this->_nowTimestamp);
			$album_id = $review_result[0]["album_id"];
			$reviews_result = $this->_Dao->select(
				"SELECT * FROM reviews WHERE album_id=:aid AND user_id<>:uid",
				array("aid"=>$album_id,"uid"=>$user_id,)
			);
			if ( count($reviews_result) === 0 ) {
			} else {
				$Syncs = new Syncs();
				foreach($reviews_result as $review){
					$syncs_calc = $Syncs->calcPoint($now_date_str, $review["created"]);
					$add_point = $syncs_calc["point"] < 0 ? 0 : $syncs_calc["point"];
					$syncs_params = array(
						"id1"=>$user_id,
						"id2"=>$review["user_id"],
						"id3"=>$user_id,
						"id4"=>$review["user_id"],
					);
					$sync_list = $this->_Dao->select(
						"SELECT id,sync_point FROM syncs WHERE user_id IN(:id1,:id2) AND user_com_id IN(:id3,:id4)",
						$syncs_params
					);
					if ( count($sync_list) === 0 ){
					} else {
						// 減算してupdate,減算した数値が0未満の場合は0に丸める
						foreach($sync_list as $sync) {
							$add_sync_point = $sync["sync_point"] - $add_point;
							$this->_Dao->update(
								"UPDATE syncs SET sync_point=:add_sync_point WHERE id=:id",
								array(
									"add_sync_point" => $add_sync_point < 0 ? 0 : $add_sync_point,
									"id" => $sync["id"],
								)
							);
						}
					}
				}
			}

			// 削除処理
			$row_count = $this->_Dao->delete(
				"DELETE FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
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
	 * edit
	 * @param int $user_id
	 * @param int $review_id
	 * @param string $listening_last
	 * @param string $listening_system
	 * @param string $body
	 * @return resultSet
	 * @throws \Exception
	 */
	public function edit( $user_id, $review_id, $listening_last, $listening_system, $body )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// レビューが存在するかチェック
			$album_result = $this->_Dao->select(
				"SELECT * FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
			if ( count($album_result) !== 1 ) {
				throw new \Exception("not found.");
			}
			// 登録
			$row_count = $this->_Dao->insert(
				 "UPDATE reviews "
				."SET body=:body,listening_last=:listening_last,listening_system=:listening_system "
				."WHERE id=:review_id",
				array(
					"review_id" => $review_id,
					"body" => $body,
					"listening_last" => $listening_last,
					"listening_system" => $listening_system,
				)
			);
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
