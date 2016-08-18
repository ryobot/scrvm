<?php
/**
 * /lib/Scrv/Helper/Reviews/Check.php
 * @author mgng
 */

namespace lib\Scrv\Helper\Reviews;
use lib\Scrv\Helper\Reviews\SituationList as SituationList;

/**
 * Check
 * @author mgng
 */
class Check
{
	/**
	 * check review post params
	 * @param array $post_params
	 * @return resultSet
	 */
	public function postParams(array $post_params)
	{
		$check_result = getResultSet();
		$SituationList = new SituationList();
		$situation_list = $SituationList->getList();
		if ( preg_match( "/\A(today|recently)\z/", $post_params["listening_last"] ) !== 1 ) {
			$check_result["messages"]["listening_last"] = "listening date が未入力です。";
		}
		$check_listening_system = false;
		foreach($situation_list as $list) {
			if ( $list["value"] === $post_params["listening_system"] ){
				$check_listening_system = true;
				break;
			}
		}
		if ( ! $check_listening_system ) {
			$check_result["messages"]["listening_system"] = "listening system が未入力です。";
		}
		if ( mb_strlen($post_params["body"]) > 1000 ) {
			$check_result["messages"]["body"] = "review は1000文字以内で入力してください。";
		}
		if ( preg_match("/\A[01]\z/", $post_params["published"]) !== 1 ) {
			$check_result["messages"]["published"] = "published が不正です。";
		}
		$check_result["status"] = count($check_result["messages"]) === 0;
		return $check_result;
	}
}
