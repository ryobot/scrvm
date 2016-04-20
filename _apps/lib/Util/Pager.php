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
	 * @param int $current_page
	 * @param int $total_rec_count
	 * @param int $page_rec
	 * @param int $show_nav
	 * @return array
	 */
	public function getPager($current_page, $total_rec_count, $page_rec=10, $show_nav=5)
	{
		$total_page = ceil($total_rec_count / $page_rec); //総ページ数
		if ($total_page < $show_nav) {
			$show_nav = $total_page;
		}

		$result = array(
			"total_count" => $total_rec_count,
			"now_page" => $current_page,
			"max_page" => $total_page,
			"offset" => $page_rec,
			"prev" => false,
			"next" => false,
			"most_prev" => false,
			"most_next" => false,
			"nav_list" => array(),
		);

		if ($total_page <= 1 || $total_page < $current_page ){
			return $result;
		}

		$show_navh = floor($show_nav / 2);
		$loop_start = $current_page - $show_navh;
		$loop_end = $current_page + $show_navh;
		if ($loop_start <= 0) {
			$loop_start  = 1;
			$loop_end = $show_nav;
		}
		if ($loop_end > $total_page) {
			$loop_start = $total_page - $show_nav +1;
			$loop_end = $total_page;
		}
		if ($current_page > 2) {
			$result["most_prev"] = true;
		}
		if ($current_page > 1) {
			$result["prev"] = true;
		}
		$nav_list = array();
		for ($i=$loop_start; $i<=$loop_end; $i++) {
			if ($i > 0 && $total_page >= $i) {
				$info = array("page" => $i, "active" => false,);
				if($i == $current_page){
					$info["active"] = true;
				}
				$nav_list[] = $info;
			}
		}
		$result["nav_list"] = $nav_list;
		if ( $current_page < $total_page){
			$result["next"] = true;
		}
		if ( $current_page < $total_page - 1) {
			$result["most_next"] = true;
		}
		return $result;
	}
}
