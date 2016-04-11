<?php
/**
 * Pager.php
 * @author mgng
 */

namespace lib\Util;

/**
 * pager class
 * @author mgng
 * @package lib\Util
 */
class Pager
{
	/**
	 * get pager
	 * @param int $offset
	 * @param int $limit
	 * @param int $total_count
	 * @return array
	 */
	public function getPager($offset,$limit, $total_count)
	{
		return array(
			"total_count" => $total_count, // データ総件数
			"max_page" => ceil($total_count/$limit), // 最大ページ数
			"limit" => $limit,	// 1ページあたりの表示数
			"offset" => $offset, // オフセット
			"now_page" => ceil($offset / $limit) + 1,	// 現在ページ
		);
	}
}
