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
		$x = 10;
		$y = 50;
		$point = (-1 * $diff * $x) + $y;
		$result["diff"] = $is_today ? $diff : null;	// today以外の場合は diff = null
		$result["point"] = $point <= 0 ? 5 : $point;	// 0以下の場合は一律 5point に変更
		return $result;
	}

	public function calcReviewDiff($review_list1, $review_list2)
	{
		// 各項目をserialize, array_diff でチェック
		$seri1 = array();
		$seri2 = array();
		foreach($review_list1 as $row1) {
			$seri1[] = serialize($row1);
		}
		foreach($review_list2 as $row2) {
			$seri2[] = serialize($row2);
		}
		// 件数が多い方からarray_diff
		$diff = array();
		if ( count($seri1) > count($seri2) ) {
			$diff = array_diff($seri1, $seri2);
		} else {
			$diff = array_diff($seri2, $seri1);
		}
		$result = array();
		foreach($diff as $row) {
			$result[] = unserialize($row);
		}
		return $result;
	}

	/**
	 * calc review point
	 * @param type $review_list user_idが重複削除されてかつcreated 昇順のリストであること
	 * @return type
	 */
	public function calcReviewsPoint($review_list)
	{
		$result = array();
		// 全ての組み合わせを計算
		foreach( $review_list as $idx1 => $review1 ) {
			foreach( $review_list as $idx2 => $review2 ) {
				if ( $idx1 === $idx2 ) {
					continue;
				}
				$sync = $this->calcPoint($review1["created"], $review2["created"]);
				if ($sync["point"] > 0) {
					$result[] = array(
						"user_id" => $review1["user_id"],
						"user_com_id" => $review2["user_id"],
						"sync" => $sync,
					);
				}
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
