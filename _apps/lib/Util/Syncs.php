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
}
