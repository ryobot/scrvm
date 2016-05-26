<?php
/**
 * Syncs.php
 * @author mgng
 */

namespace lib\Util;

/**
 * Syncs class
 * @author mgng
 * @package lib\Util
 */
class Syncs
{
	/**
	 * 日付を比較してsyncポイントを計算して日付の差とpointを返す
	 * @param string $created1 日付文字列
	 * @param string $created2 日付文字列
	 * @param string $listening_last today/recently
	 * @return array
	 */
	public function calcPoint($created1, $created2, $listening_last = "today")
	{
		$time1 = strtotime($created1);
		$time2 = strtotime($created2);
		$result = array(
			"diff" => -1,
			"point" => 0,
		);
		if ( $time1 === false || $time2 === false ) {
			return $result;
		}

		$is_today = $listening_last === "today";

		// point = -(diff * x * x) + y
		$diff = (int)floor(abs($time1-$time2)/(60*60*24));
		$x = 3;
		$y = 50;
		$point = (-1 * $diff * $x * $x) + $y;
		$result["diff"] = $is_today ? $diff : null;	// today以外の場合は diff = null
		$result["point"] = $is_today ? $point : 10;	// today以外の場合は一律 10point
		return $result;
	}

	public function calcReviewsPoint($review_list)
	{
		$result = array();
		for($i=0, $len=count($review_list); $i<$len; $i++){
			$current = $review_list[$i];
			$next = isset($review_list[$i+1]) ? $review_list[$i+1] : null;
			if ( $next === null ) {
				break;
			}
			if ($current["user_id"] === $next["user_id"]) {
				continue;
			}
			// pointが0以上の場合のみ
			$sync = $this->calcPoint($current["created"], $next["created"], $next["listening_last"]);
			if ($sync["point"] > 0) {
				$result[] = array(
					"user_id" => $current["user_id"],
					"user_com_id" => $next["user_id"],
					"sync" => $sync,
				);
			}
		}
		return $result;
	}

	public function calcFavTracksPoint($user_id_list)
	{
		$result = array();
		for($i=0,$len=count($user_id_list); $i<$len; $i++) {
			$idx = $user_id_list[$i]["user_id"];
			for( $j=0;$j<$len; $j++) {
				if ( $idx === $user_id_list[$j]["user_id"] ) {
					continue;
				}
				$key = "{$idx}_{$user_id_list[$j]["user_id"]}";
				if ( ! isset($result[$key]) ) {
					$result[$key] = 0;
				}
				$result[$key] += 2;	// fav_tracks は 2ポイント
			}
		}
		return $result;
	}

	public function calcFavAlbumsPoints($user_id_list)
	{
		$result = array();
		for($i=0,$len=count($user_id_list); $i<$len; $i++) {
			$idx = $user_id_list[$i]["user_id"];
			for( $j=0;$j<$len; $j++) {
				if ( $idx === $user_id_list[$j]["user_id"] ) {
					continue;
				}
				$key = "{$idx}_{$user_id_list[$j]["user_id"]}";
				if ( ! isset($result[$key]) ) {
					$result[$key] = 0;
				}
				$result[$key] += 5;	// fav_albums は 5ポイント
			}
		}
		return $result;
	}
}
