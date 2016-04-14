<?php
/**
 * lib/Scrv/Dao/Albums.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Albums class
 * @author mgng
 */
class Albums extends Dao
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
	 * lists
	 * @param int $offset
	 * @param int $limit
	 * @param string $artist
	 * @param string $sort artist,title,year のいずれか
	 * @param string $order asc, desc のいずれか
	 * @return resultSet
	 */
	public function lists($offset,$limit, $artist, $sort, $order)
	{
		$result = getResultSet();
		try{
			$orderby = "ORDER BY {$sort} {$order}";
			$offsetlimit = "LIMIT {$offset},{$limit}";
			$sql = "SELECT * FROM albums {$orderby} {$offsetlimit}";
			$sql_count = "SELECT count(id) cnt FROM albums";
			$params = array();
			if ( $artist !== null && $artist !== "" ) {
				$where = "WHERE(artist like :artist ESCAPE '!')";
				$sql = "SELECT * FROM albums {$where} {$orderby} {$offsetlimit}";
				$sql_count = "SELECT count(id) cnt FROM albums {$where} ";
				$params = array("artist"=>"%".$this->_Dao->escapeForLike($artist)."%");
			}
			$albums_result = $this->_Dao->select($sql, $params);
			$albums_count_result = $this->_Dao->select($sql_count, $params);
			$result["status"] = true;
			$result["data"] = array(
				"lists" => $albums_result,
				"lists_count" => $albums_count_result[0]["cnt"],
			);
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * View Album Data
	 * @param int $id
	 * @return resultSet
	 * @throws \Exception
	 */
	public function view($id)
	{
		$result = getResultSet();
		try{
			// album 情報
			$album_result = $this->_Dao->select(
				 "SELECT t1.*, t2.favalbums_count FROM albums t1 "
				."LEFT JOIN (SELECT album_id,count(id) AS favalbums_count FROM favalbums GROUP BY album_id) t2 ON(t1.id=t2.album_id) "
				."WHERE t1.id=:id",
				array("id" => $id,));
			if ( count($album_result) !== 1 ) {
				throw new \Exception("album not found.");
			}
			$album_data = $album_result[0];
			// track 情報
			$tracks_data = $this->_Dao->select(
				 "SELECT t1.*, t2.favtracks_count FROM tracks t1 "
				."LEFT JOIN (SELECT track_id,count(id) AS favtracks_count FROM favtracks GROUP BY track_id) t2 ON(t1.id=t2.track_id) "
				."WHERE t1.album_id=:id "
				."ORDER BY t1.track_num",
				array("id" => $id,)
			);
			if ( count($tracks_data) === 0 ) {
				throw new \Exception("track not found.");
			}
			// review内容取得
			$review_data = $this->_Dao->select(
				 "SELECT t1.*, t2.username, t2.img_file FROM reviews t1 "
				."INNER JOIN users t2 ON(t1.user_id=t2.id) "
				."WHERE t1.album_id=:album_id ORDER BY t1.created DESC",
				array("album_id" => $album_data["id"],)
			);
			$result["status"] = true;
			$result["data"] = array(
				"album" => $album_data,
				"tracks" => $tracks_data,
				"reviews" => $review_data,
			);
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * album add
	 * @param string $artist
	 * @param string $title
	 * @param string $year
	 * @param string $img_url
	 * @param string $img_file
	 * @param array $tracks
	 * @return resultSet
	 */
	public function add($artist, $title, $year, $img_url, $img_file, array $tracks)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// 同一アルバムがある場合はエラー(mysqlは大文字小文字区別しない)
			$search_result = $this->_Dao->select(
				"SELECT * FROM albums WHERE artist=:artist AND title=:title",
				array("artist" => $artist,"title" => $title,)
			);
			if ( count($search_result) !== 0 ) {
				throw new \Exception("{$artist} / {$title} は登録済みです。");
			}
			// INSERT, img_url, year が空文字の場合は null
			$this->_Dao->insert(
				 "INSERT INTO albums (artist,title,img_url,img_file,year,created,modified) "
				."VALUES(:artist,:title,:img_url,:img_file,:year,now(),now())",
				array(
					"artist" => $artist,
					"title" => $title,
					"img_url" => $img_url === "" ? null : $img_url,
					"img_file" => $img_file === "" ? null : $img_file,
					"year" => $year === "" ? null : $year,
				)
			);
			// 登録したalbum id を取得
			$album_result = $this->_Dao->select(
				"SELECT id FROM albums WHERE artist=:artist AND title=:title",
				array("artist" => $artist,"title" => $title,)
			);
			if ( count($album_result) === 0 ) {
				throw new \Exception("{$artist} / {$title} の登録に失敗しました。");
			}
			$album_id = $album_result[0]["id"];
			// tracks をinsert
			$track_index = 1;
			foreach( $tracks as $track_title ) {
				$this->_Dao->insert(
					 "INSERT INTO tracks (artist,album_id,track_num,track_title,created) "
					."VALUES(:artist,:album_id,:track_num,:track_title,now())",
					array(
						"artist" => $artist,
						"album_id" => (int)$album_id,
						"track_num" => $track_index,
						"track_title" => $track_title,
					)
				);
				$track_index++;
			}
			$result["status"] = true;
			$result["data"]["album_id"] = $album_id;
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
	 * favalbums by album_id and user_id
	 * @param integer $album_id
	 * @param integer $user_id
	 * @return resultSet
	 */
	public function favalbums( $album_id, $user_id )
	{
		$result = getResultSet();
		try{
			$search_result = $this->_Dao->select(
				 "SELECT t1.*, t2.user_id FROM albums t1 "
				."INNER JOIN favalbums t2 ON (t1.id=t2.album_id) "
				."WHERE t1.id=:album_id AND t2.user_id=:user_id",
				array("album_id" => $album_id, "user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"] = $search_result;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * favalbums by user_id
	 * @param int $user_id
	 * @param int $offset
	 * @param int $limit
	 * @return resultSet
	 */
	public function favalbumsByUserId( $user_id, $offset, $limit )
	{
		$result = getResultSet();
		try{
			$search_result = $this->_Dao->select(
				 "SELECT t1.* FROM albums t1 "
				."INNER JOIN favalbums t2 ON (t1.id=t2.album_id) "
				."WHERE t2.user_id=:user_id "
				."ORDER BY t2.created DESC LIMIT {$offset},{$limit}",
				array("user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"] = $search_result;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * fav update
	 * @param integer $album_id
	 * @param integer $user_id
	 * @return string
	 */
	public function fav( $album_id, $user_id )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// sync point (album)
			$add_point = 5;
			// 存在したらdeleteでpoint減算, しなければinsert or updateで加算
			$params = array("album_id" => $album_id,"user_id" => $user_id,);
			$sel_result = $this->_Dao->select("SELECT * FROM favalbums WHERE album_id=:album_id AND user_id=:user_id", $params);
			$oparation = "delete";
			if ( count($sel_result) > 0 ) {
				$this->_Dao->delete("DELETE FROM favalbums WHERE album_id=:album_id AND user_id=:user_id",$params);
				$add_point = -5;
				$oparation = "delete";
			} else {
				$this->_Dao->insert("INSERT INTO favalbums (favtype,album_id,user_id,created) VALUES('alltime',:album_id,:user_id,now())", $params);
				$oparation = "insert";
			}

			// XXX ...
			// favalbums テーブルを参照して同じ album_id を登録しているユーザ一覧を取得、なければ終わり
			$fav_user_id_list = $this->_Dao->select(
				"SELECT user_id FROM favalbums WHERE album_id=:album_id AND user_id<>:user_id",
				$params
			);
			if (count($fav_user_id_list) === 0) {
			} else {
				// syncs.sync_pointが存在するか確認
				foreach ($fav_user_id_list as $fav_user_id) {
					$syncs_params = array(
						"id1" => $fav_user_id["user_id"],
						"id2" => $user_id,
						"id3" => $fav_user_id["user_id"],
						"id4" => $user_id,
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
							$add_sync_point = $sync["sync_point"] + $add_point < 0 ? 0 : $sync["sync_point"] + $add_point;
							$this->_Dao->update(
								"UPDATE syncs SET sync_point=:add_sync_point WHERE id=:id",
								array("add_sync_point" => $add_sync_point,"id" => $sync["id"],)
							);
						}
					}
				}
			}

			$result["status"] = true;
			$result["data"] = array(
				"operation" => $oparation,
			);
			$this->_Dao->commit();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

	/**
	 * favCount
	 * @param int $album_id
	 * @return resultSet
	 */
	public function favCount($album_id)
	{
		$result = getResultSet();
		try{
			$search_result = $this->_Dao->select("
				SELECT count(id) AS cnt FROM favalbums WHERE album_id=:aid",
				array("aid" => $album_id,)
			);
			$result["status"] = true;
			$result["data"] = array(
				"count" => $search_result[0]["cnt"],
			);
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

}


