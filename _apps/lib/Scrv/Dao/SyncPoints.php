<?php
/**
 * lib/Scrv/Dao/SyncPoints.php
 *
 * sync point 一括設定用
 *
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;
use lib\Util\Syncs as Syncs;

/**
 * SyncPoints class
 * @author tomita
 */
class SyncPoints extends Dao
{
	/**
	 * Dao object
	 * @var Dao
	 */
	private $_Dao = null;

	/**
	 * Syncs Object
	 * @var Syncs
	 */
	private $_Syncs = null;

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
		$this->_Syncs = new Syncs();
		return true;
	}

	/**
	 * sync points update (all)
	 * @param int $user_com_id 更新実行した user_id。default null
	 * @return resultSet
	 * @throws \Exception
	 */
	public function updateSyncPoints()
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// レビューpointの計算
			$sync_points_reviews = $this->calcPointsReviews();
			// fav.tracks ポイント計算
			$sync_points_favtracks = $this->calcPointsFavTracks();
			// fav.albums ポイント計算
			$sync_points_favalbums = $this->calcPointsFavAlbums();

			// 全ポイントマージ
			$sync_points = $this->mergePoints(
				$sync_points_reviews,
				$sync_points_favtracks,
				$sync_points_favalbums
			);

			// update
			$this->updatePoints($sync_points);

			$result["status"] = true;
			$result["data"] = null;
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
	 * calc review point
	 * @return array
	 */
	public function calcPointsReviews()
	{
		$sync_point_data = array();

		// レビューが複数存在するアルバムID一覧を取得する
		$sql = "SELECT t1.id FROM albums t1
			INNER JOIN reviews t2 ON (t2.album_id=t1.id)
			GROUP BY t1.id HAVING count(t1.id) > 1
			ORDER BY t1.id
		";
		$album_id_lists = $this->_Dao->select($sql);
		if ( count($album_id_lists) === 0 ) {
			return $sync_point_data;
		}

		// album_id ごとに 古いものからレヴューを取得して計算
		foreach( $album_id_lists as $row ) {
			$album_id = $row["id"];

			// 複数ユーザレビューがなければスルー
			$user_count = $this->_Dao->select(
				"SELECT user_id FROM reviews WHERE album_id=:aid GROUP BY user_id"
				,array("aid" => $album_id)
			);
			if ( count($user_count) < 2 ) {
				continue;
			}

			// アルバムレビューごとにsyncを計算。重複user_idはgroup by で除外（先にレビューを書いたIDを優先）
			$review_list = $this->_Dao->select(
				"SELECT * FROM reviews WHERE album_id=:aid GROUP BY user_id ORDER BY created"
				, array("aid" => $album_id)
			);
			$sync_point_result = $this->_Syncs->calcReviewsPoint($review_list);
			foreach($sync_point_result as $sync_point) {
				$idx = "{$sync_point["user_id"]}_{$sync_point["user_com_id"]}";
				if ( !isset( $sync_point_data[$idx] ) ) {
					$sync_point_data[$idx] = 0;
				}
				$sync_point_data[$idx] += $sync_point["sync"]["point"];
			}
		}
		return $sync_point_data;
	}

	/**
	 * calc favtracks point
	 * @return type
	 */
	public function calcPointsFavTracks()
	{
		$sync_point_data = array();

		// 複数favされているtrack_idを取得
		$sql = "SELECT t1.track_id FROM favtracks t1 GROUP BY t1.track_id HAVING count(t1.track_id) > 1";
		$params = array();
		$track_id_list = $this->_Dao->select($sql, $params);

		// track_id ごとに user_id を取得
		foreach($track_id_list as $row) {
			$track_id = $row["track_id"];
			$user_id_list = $this->_Dao->select(
				"SELECT user_id FROM favtracks WHERE track_id=:tid"
				,array("tid" => $track_id)
			);
			$favtracks_points = $this->_Syncs->calcFavTracksPoint($user_id_list);
			// sync_point_data に加算
			foreach( $favtracks_points as $key => $point ) {
				if ( ! isset($sync_point_data[$key]) ) {
					$sync_point_data[$key] = 0;
				}
				$sync_point_data[$key] += $point;
			}
		}
		return $sync_point_data;
	}

	/**
	 * calc favalbums point
	 * @return type
	 */
	public function calcPointsFavAlbums()
	{
		$sync_point_data = array();

		$sql = "SELECT t1.album_id FROM favalbums t1 GROUP BY t1.album_id HAVING count(t1.album_id) > 1";
		$params = array();

		// 複数favされているalbum_idを取得
		$album_id_list = $this->_Dao->select($sql, $params);
		// album_id ごとに user_id を取得
		foreach($album_id_list as $row) {
			$album_id = $row["album_id"];
			$user_id_list = $this->_Dao->select(
				"SELECT user_id FROM favalbums WHERE album_id=:aid"
				, array("aid" => $album_id)
			);
			$favalbums_points = $this->_Syncs->calcFavAlbumsPoints($user_id_list);
			// sync_point_data に加算
			foreach( $favalbums_points as $key => $point ) {
				if ( ! isset($sync_point_data[$key]) ) {
					$sync_point_data[$key] = 0;
				}
				$sync_point_data[$key] += $point;
			}
		}
		return $sync_point_data;
	}

	/**
	 * 各sync point をマージする。引数は可変長。
	 * @return type
	 */
	private function mergePoints()
	{
		$sync_points = array();
		$args = func_get_args();
		for($i=0,$len=count($args); $i<$len; $i++){
			foreach($args[$i] as $key => $point){
				if ( ! isset($sync_points[$key]) ) {
					$sync_points[$key] = 0;
				}
				$sync_points[$key] += $point;
			}
		}
		return $sync_points;
	}

	/**
	 * update points
	 * @param array $sync_points
	 * @return boolean
	 */
	private function updatePoints($sync_points)
	{
		// 全体更新する場合は最初に 0 update
		$this->_Dao->update("UPDATE syncs SET sync_point=0");

		// 各user_id毎にinsert/update
		foreach( $sync_points as $key=>$point ) {
			$tmp = explode("_", $key);
			$_user_id = (int)$tmp[0];
			$_user_com_id = (int)$tmp[1];
			// 存在したらupdate、しなければinsert
			$sync_list = $this->_Dao->select(
				"SELECT * FROM syncs WHERE user_id=:uid AND user_com_id=:ucid"
				,array("uid" => $_user_id, "ucid" => $_user_com_id,)
			);
			$sql = "UPDATE syncs SET sync_point=:sp WHERE user_id=:uid AND user_com_id=:ucid";
			$params = array("uid"=>$_user_id, "ucid"=>$_user_com_id, "sp"=>$point,);
			if ( count($sync_list) === 0 ) {
				$sql = "INSERT INTO syncs (user_id,user_com_id,sync_point) VALUES(:uid,:ucid,:sp)";
			}
			$this->_Dao->update($sql, $params);
		}
		return true;
	}

}
