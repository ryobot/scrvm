<?php
/**
 * lib/Scrv/Dao/Logs.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;
//use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
//use lib\Util\Syncs as Syncs;

/**
 * Reviews class
 * @author tomita
 */
class Logs extends Dao
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
	 * @param int $user_id null OK
	 * @param string $hashtag default null
	 * @return resultSet
	 */
	public function lists( $offset, $limit, $user_id, $hashtag = null, $situation = null, $select_user = null )
	{
		$result = getResultSet();
		try{

			// DBファイルキャッシュ設定
			$db_cache_setting = array(
				"enabled" => true,
				"expire" => 3,
				"index" => "Review_index",
			);

			$my_fav_select = "";
			$my_fav_sql = "";
			$hashtags_sql = "";
			$situation_sql = "";
			$select_user_sql = "";
			$params = array();
			$params_count = array();
			if ( isset($user_id) ) {
				$my_fav_select = ",t6.id as my_fav_id";
				$my_fav_sql = "LEFT JOIN favreviews t6 ON(t1.id=t6.review_id AND t6.user_id=:uid)";
				$params = array("uid" => $user_id);
			}
			if ( isset($hashtag) ) {
				$hashtags_sql = "INNER JOIN hashtags t7 ON(t1.id=t7.review_id AND t7.tag=:hashtag)";
				$params["hashtag"] = $hashtag;
				$params_count["hashtag"] = $hashtag;
			}
			if ( isset($situation) ) {
				$situation_sql = "WHERE t1.listening_system=:situation";
				$params["situation"] = $situation;
				$params_count["situation"] = $situation;
			}
			if ( isset($select_user) ) {
				$select_user_sql = "WHERE t1.user_id=:select_user";
				$params["select_user"] = $select_user;
				$params_count["select_user"] = $select_user;
			}
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks
				,t3.username,t3.img_file AS user_img_file
				,(SELECT count(t4.id) FROM reviews t4 WHERE t1.album_id=t4.album_id) as reviews_count
				,(SELECT count(t5.id) FROM favreviews t5 WHERE t1.id=t5.review_id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN albums t2 ON(t1.album_id=t2.id)
				INNER JOIN users t3 ON(t1.user_id=t3.id)
				{$hashtags_sql}
				{$my_fav_sql}
				{$situation_sql}
				{$select_user_sql}
				ORDER BY t1.created DESC
				LIMIT {$offset},{$limit}",
				$params,
				$db_cache_setting
			);
			$data_count = $this->_Dao->select("
				SELECT count(t1.id) cnt
				FROM reviews t1
				{$hashtags_sql}
				{$select_user_sql}
				{$situation_sql}",
				$params_count,
				$db_cache_setting
			);
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

}
