<?php
/**
 * lib/Scrv/Dao/Albums.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Posts class
 * @author mgng
 */
class Albums extends Dao
{
	/**
	 * lists
	 * @param integer $offset
	 * @param integer $limit
	 * @return getResult
	 */
	public function lists($offset,$limit, $artist)
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			$sql = "SELECT * FROM albums ORDER BY artist, title LIMIT {$offset}, {$limit}";
			$sql_count = "SELECT count(id) cnt FROM albums";
			$params = array();
			if ( $artist !== null && $artist !== "" ) {
				$sql = "SELECT * FROM albums WHERE (artist like :artist ESCAPE '!') ORDER BY artist, title LIMIT {$offset}, {$limit}";
				$sql_count = "SELECT count(id) cnt FROM albums WHERE (artist like :artist ESCAPE '!') ";
				$params = array(
					"artist" => "%" . $Dao->escapeForLike($artist) . "%"
				);
			}
			$albums_result = $Dao->select($sql, $params);
			$albums_count_result = $Dao->select($sql_count, $params);

			$result["status"] = true;
			$result["data"]["lists"] = $albums_result;
			$result["data"]["lists_count"] = $albums_count_result[0]["cnt"];
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
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
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			// アルバム情報
			$album_result = $Dao->select("SELECT * FROM albums WHERE id=:id", array("id" => $id,));
			if ( count($album_result) !== 1 ) {
				throw new \Exception("album not found.");
			}
			$album_data = $album_result[0];

			// track情報
			$tracks_result = $Dao->select(
				 "SELECT t1.*, t2.favtracks_count "
				."FROM tracks t1 "
				."LEFT JOIN (SELECT track_id, count(id) AS favtracks_count FROM favtracks GROUP BY track_id) t2 ON (t1.id=t2.track_id) "
				."WHERE t1.album_id=:id "
				."ORDER BY t1.track_num",
				array("id" => $id,)
			);
			if ( count($tracks_result) === 0 ) {
				throw new \Exception("track not found.");
			}
			$tracks_data = $tracks_result;

			// レビュー内容取得
			$review_result = $Dao->select(
				 "SELECT t1.*, t2.username, t2.img_file "
				."FROM reviews t1 "
				."INNER JOIN users t2 ON (t1.user_id=t2.id) "
				."WHERE t1.album_id=:album_id ORDER BY t1.created DESC",
				array("album_id" => $album_data["id"],)
			);

			$result["status"] = true;
			$result["data"] = array(
				"album" => $album_data,
				"tracks" => $tracks_data,
				"reviews" => $review_result,
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
	public function add( $artist, $title, $year, $img_url, $img_file, array $tracks )
	{
		$result = getResultSet();

		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}

		$Dao->beginTransaction();
		try{
			// 同一アルバムがある場合はエラー(mysqlは大文字小文字区別しない)
			$search_result = $Dao->select(
				"SELECT * FROM albums WHERE artist=:artist AND title=:title", array(
				"artist" => $artist,
				"title" => $title,
			));
			if ( count($search_result) !== 0 ) {
				throw new \Exception("{$artist} / {$title} は登録済みです。");
			}

			// INSERT, img_url, year が空文字の場合は null
			$Dao->insert(
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
			$album_result = $Dao->select(
				"SELECT id FROM albums WHERE artist=:artist AND title=:title",array(
				"artist" => $artist,
				"title" => $title,
			));
			if ( count($album_result) === 0 ) {
				throw new \Exception("{$artist} / {$title} の登録に失敗しました。");
			}
			$album_id = $album_result[0]["id"];

			// tracks をinsert
			$track_index = 1;
			foreach( $tracks as $track_title ) {
				$Dao->insert(
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
