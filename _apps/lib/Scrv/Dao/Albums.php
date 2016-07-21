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
		if ( ! $this->_Dao->connect(self::$_common_ini["db"]) ) {
			echo $this->_Dao->getErrorMessage();
			exit;
		}
		return true;
	}

	/**
	 * lists
	 * @param int $offset
	 * @param int $limit
	 * @param string $stype
	 * @param string $type
	 * @param string $q
	 * @param string $index
	 * @param string $sort artist,title,year のいずれか
	 * @param string $order asc, desc のいずれか
	 * @return resultSet
	 */
	public function lists($offset,$limit, $stype, $type, $q, $index, $sort, $order)
	{
		$result = getResultSet();
		try{
			if ($stype === "index") {
				$result = $this->_listsByIndex($offset, $limit, $type, $index, $sort, $order);
			} else {
				$result = $this->_listsBySearch($offset, $limit, $type, $q, $sort, $order);
			}
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	private function _listsByIndex($offset,$limit, $type, $index, $sort, $order)
	{
		$result = getResultSet();
		$where = "WHERE({$type} REGEXP '^({$index}|the {$index})')";
		// T の場合は the は除外
		if ( strtolower($index) === "t" ) {
			$where = "WHERE({$type} REGEXP '^{$index}' AND {$type} NOT REGEXP '^the ')";
		}
		$left_join = "LEFT JOIN reviews t2 ON(t1.id=t2.album_id)";
		$group_by = "GROUP BY t1.id";
		$orderby = "ORDER BY {$sort} {$order}";
		$offsetlimit = "LIMIT {$offset},{$limit}";
		$sql = "SELECT t1.*, count(t2.id) AS reviews FROM albums t1 {$left_join} {$where} {$group_by} {$orderby} {$offsetlimit}";
		$sql_count = "SELECT count(id) cnt FROM albums {$where} ";
		$params = array();
		if ( $index === "日" ) {
			$where = "WHERE({$type} NOT REGEXP '^([a-zA-Z0-9])')";
			$sql = "SELECT t1.*, count(t2.id) AS reviews FROM albums t1 {$left_join} {$where} {$group_by} {$orderby} {$offsetlimit}";
			$sql_count = "SELECT count(id) cnt FROM albums {$where} ";
		}

		// DBファイルキャッシュ設定
		$db_cache_setting = array(
			"enabled" => true,
			"expire" => 3,
			"index" => "Albums_Index_ListsByIndex",
		);

		$albums_result = $this->_Dao->select($sql, $params, $db_cache_setting);
		$albums_count_result = $this->_Dao->select($sql_count, $params, $db_cache_setting);
		$result["status"] = true;
		$result["data"] = array(
			"lists" => $albums_result,
			"lists_count" => $albums_count_result[0]["cnt"],
		);
		return $result;
	}

	private function _listsBySearch($offset,$limit, $type, $q, $sort, $order)
	{
		$result = getResultSet();
		$left_join = "LEFT JOIN reviews t2 ON(t1.id=t2.album_id)";
		$group_by = "GROUP BY t1.id";
		$orderby = "ORDER BY {$sort} {$order}";
		$offsetlimit = "LIMIT {$offset},{$limit}";
		$sql = "SELECT t1.*, count(t2.id) AS reviews FROM albums t1 {$left_join} {$group_by} {$orderby} {$offsetlimit}";
		$sql_count = "SELECT count(id) cnt FROM albums";
		$params = array();
		if ( $q !== null && $q !== "" ) {
			$where = "WHERE({$type} like :q ESCAPE '!')";
			$sql = "SELECT t1.*, count(t2.id) AS reviews FROM albums t1 {$left_join} {$where} {$group_by} {$orderby} {$offsetlimit}";
			$sql_count = "SELECT count(id) cnt FROM albums {$where} ";
			$params = array("q"=>"%".$this->_Dao->escapeForLike($q)."%");
		}

		// DBファイルキャッシュ設定
		$db_cache_setting = array(
			"enabled" => true,
			"expire" => 3,
			"index" => "Albums_Index_ListsBySearch",
		);

		$albums_result = $this->_Dao->select($sql, $params, $db_cache_setting);
		$albums_count_result = $this->_Dao->select($sql_count, $params, $db_cache_setting);
		$result["status"] = true;
		$result["data"] = array(
			"lists" => $albums_result,
			"lists_count" => $albums_count_result[0]["cnt"],
		);
		return $result;
	}

	/**
	 * View Album Data
	 * @param int $id
	 * @return resultSet
	 * @throws \Exception
	 */
	public function view($id, $user_id = null )
	{
		$result = getResultSet();
		try{
			// album 情報
			$album_result = $this->_Dao->select("
				SELECT t1.*, t2.favalbums_count
				FROM albums t1
				LEFT JOIN (SELECT album_id,count(id) AS favalbums_count FROM favalbums GROUP BY album_id) t2 ON(t1.id=t2.album_id)
				WHERE t1.id=:id",
				array("id" => $id,));
			if ( count($album_result) !== 1 ) {
				throw new \Exception("album not found.");
			}
			$album_data = $album_result[0];
			// tags 情報
			$tags_result = $this->_Dao->select("
				SELECT t1.* FROM tags t1
				INNER JOIN albums t2 ON (t2.id=t1.album_id)
				WHERE t1.album_id=:id",
				array("id" => $id,)
			);
			$tags_data = $tags_result;
			// track 情報
			$tracks_data = $this->_Dao->select("
				SELECT t1.*, t2.favtracks_count
				FROM tracks t1
				LEFT JOIN (SELECT track_id,count(id) AS favtracks_count FROM favtracks GROUP BY track_id) t2 ON(t1.id=t2.track_id)
				WHERE t1.album_id=:id
				ORDER BY t1.track_num",
				array("id" => $id,)
			);
			if ( count($tracks_data) === 0 ) {
				throw new \Exception("track not found.");
			}
			// review内容取得
			$my_fav_select = "";
			$my_fav_sql = "";
			$params = array("album_id" => $album_data["id"],);
			if ( isset($user_id) ) {
				$my_fav_select = ",t6.id as my_fav_id";
				$my_fav_sql = "LEFT JOIN favreviews t6 ON(t1.id=t6.review_id AND t6.user_id=:uid)";
				$params["uid"] = $user_id;
			}
			$review_data = $this->_Dao->select("
				SELECT
				t1.*
				,t3.username,t3.img_file AS user_img_file
				,count(t5.id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN users t3 ON(t1.user_id=t3.id)
				LEFT JOIN favreviews t5 ON(t1.id=t5.review_id)
				{$my_fav_sql}
				WHERE t1.album_id=:album_id
				GROUP BY t1.id
				ORDER BY t1.created DESC",
				$params
			);
			$result["status"] = true;
			$result["data"] = array(
				"album" => $album_data,
				"tags" => $tags_data,
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
	 * View Album Data by tag
	 * @param string $tag
	 * @param string $artist
	 * @param string $sort
	 * @param string $order
	 * @param int $offset
	 * @param int $limit
	 * @return resultSet
	 */
	public function tag($tag, $artist, $sort, $order, $offset, $limit)
	{
		$result = getResultSet();
		try{
			$orderby = "ORDER BY t1.{$sort} {$order}";
			$offsetlimit = "LIMIT {$offset},{$limit}";
			$where = "";
			$params = array("tag" => $tag);
			if ( $artist !== null && $artist !== "" ) {
				$where = "AND (artist like :artist ESCAPE '!')";
				$params["artist"] = "%".$this->_Dao->escapeForLike($artist)."%";
			}
			$sql = "SELECT
				t1.*
				,count(t2.id) AS reviews
				FROM albums t1
				LEFT JOIN reviews t2 ON (t2.album_id=t1.id)
				WHERE t1.id IN (SELECT album_id FROM tags WHERE tag=:tag)
				{$where}
				GROUP BY t1.id
				{$orderby}
				{$offsetlimit}";
			$sql_count = "SELECT count(id) as cnt FROM albums WHERE id IN (SELECT album_id FROM tags WHERE tag=:tag) {$where}";
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
	 * album exists
	 * @param string $artist
	 * @param string $title
	 * @return resultSet
	 */
	public function exists($artist, $title)
	{
		$result = getResultSet();
		try{
			// mysql は大文字小文字区別しない
			$search_result = $this->_Dao->select(
				"SELECT * FROM albums WHERE artist=:artist AND title=:title",
				array("artist" => $artist,"title" => $title,)
			);
			$result["status"] = true;
			$result["data"] = $search_result;
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
	 * @param int $user_id
	 * @return resultSet
	 */
	public function add($artist, $title, $year, $img_url, $img_file, array $tracks, $user_id = null)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// 同一アルバム検索
			$search_result = $this->exists($artist, $title);
			if ( ! $search_result["status"] || count($search_result["data"]) !== 0 ) {
				throw new \Exception("{$artist} / {$title} は登録済みです。");
			}
			// INSERT, img_url, year が空文字の場合は null
			$this->_Dao->insert(
				 "INSERT INTO albums (artist,title,img_url,img_file,year,create_user_id,created,modified) "
				."VALUES(:artist,:title,:img_url,:img_file,:year,:cuid,now(),now())",
				array(
					"artist" => $artist,
					"title" => $title,
					"img_url" => $img_url === "" ? null : $img_url,
					"img_file" => $img_file === "" ? null : $img_file,
					"year" => $year === "" ? null : $year,
					"cuid" => $user_id,
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
			// tags をinsert
			$this->_Dao->insert("
				INSERT INTO tags (album_id,create_user_id,tag,can_be_deleted,created)
				VALUES(:album_id,:cuid,:tag,0,now())",
				array(
					"album_id" => $album_id,
					"cuid" => $user_id,
					"tag" => $artist,
				)
			);
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
			$oparation = "insert";
			// 存在したらdeleteでpoint減算, しなければinsert or updateで加算
			$params = array("aid" => $album_id,"uid" => $user_id,);
			$sel_result = $this->_Dao->select("SELECT * FROM favalbums WHERE album_id=:aid AND user_id=:uid", $params);
			if ( count($sel_result) > 0 ) {
				$this->_Dao->delete("DELETE FROM favalbums WHERE album_id=:aid AND user_id=:uid",$params);
				$add_point = -5;
				$oparation = "delete";
			} else {
				$this->_Dao->insert("INSERT INTO favalbums (favtype,album_id,user_id,created) VALUES('alltime',:aid,:uid,now())", $params);
			}

			// add sync point
			$this->_addSyncPoints($album_id, $user_id, $add_point);

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

	private function _addSyncPoints($album_id, $user_id, $add_point)
	{
		$params = array("aid" => $album_id,"uid" => $user_id,);
		$fav_user_id_list = $this->_Dao->select("SELECT user_id FROM favalbums WHERE album_id=:aid AND user_id<>:uid",$params);
		if (count($fav_user_id_list) === 0) {
			return true;
		}
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

	/**
	 * save album and tracks
	 * @param int $id album_id
	 * @param string $artist
	 * @param string $title
	 * @param string $year
	 * @param string $img_file
	 * @param array $tracks
	 * @param int $user_id
	 * @return resultSet
	 * @throws \Exception
	 */
	public function save($id, $artist, $title, $year, $img_file, array $tracks, $user_id)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// check album exist
			$search_result = $this->_Dao->select(
//				"SELECT * FROM albums WHERE id=:id AND create_user_id=:uid",
//				array("id"=>$id, "uid"=>$user_id,)
				"SELECT * FROM albums WHERE id=:id",
				array("id"=>$id,)
			);
			if ( count($search_result) === 0 ) {
				throw new \Exception("該当のアルバムが見つかりません。");
			}
			// update album $img_file が null の場合は無視
			$update_sql = "UPDATE albums SET artist=:artist,title=:title,img_file=:img_file,year=:year,modified=now() WHERE id=:id";
			$update_params = array(
				"artist" => $artist,
				"title" => $title,
				"img_file" => $img_file,
				"year" => $year === "" ? null : $year,
				"id" => $id,
			);
			if ($img_file === null) {
				$update_sql = "UPDATE albums SET artist=:artist,title=:title,year=:year,modified=now() WHERE id=:id";
				unset($update_params["img_file"]);
			}
			$this ->_Dao->update($update_sql,$update_params);
			// update tracks
			foreach( $tracks as $idx => $track ) {
				$track_num = $idx+1;
				// track->id がない場合はinsert
				if ( $track->id === "" ) {
					$this->_Dao->insert("
						INSERT INTO tracks
						(artist,album_id,track_num,track_title,created)
						values(:artist,:album_id,:track_num,:track_title,now())",
						array(
							"artist" => $track->artist,
							"album_id" => $id,
							"track_num" => $track_num,
							"track_title" => $track->track_title,
						)
					);
				} else {
					$this->_Dao->update("
						UPDATE tracks
						SET track_title=:track_title, track_num=:track_num
						WHERE album_id=:album_id AND id=:id",
						array(
							"track_title" => $track->track_title,
							"track_num" => $track_num,
							"album_id" => $id,
							"id" => $track->id,
						)
					);
				}
			}
			$result["status"] = true;
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

