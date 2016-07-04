<?php
/**
 * /lib/Scrv/Helper/Reviews/SituationList.php
 * @author mgng
 */

namespace lib\Scrv\Helper\Reviews;
use lib\Scrv\Action\Base as Base;

/**
 * SituationList
 * @author mgng
 */
class SituationList extends Base
{
	/**
	 * get situation list
	 * @return array
	 */
	public function getList()
	{
		$base_dir = "img/situation/";
		$search_dir = __DIR__ . "/../../../../../{$base_dir}";
		$list = glob($search_dir. "*.svg");
		$result = array();
		foreach( $list as $real_path ) {
			$filename = basename($real_path);
			$result[] = array(
				"real_path" => $real_path,
				"filename" => $filename,
				"path" => $base_dir . $filename,
				"value" => preg_replace("/\.svg\z/", "", $filename),
			);
		}
		return $result;
	}

}
