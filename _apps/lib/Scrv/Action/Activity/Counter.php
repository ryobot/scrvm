<?php
/**
 * /lib/Scrv/Action/Activity/Counter.php
 * @author mgng
 */

namespace lib\Scrv\Action\Activity;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Activity as DaoActivity;
use lib\Util\Server as Server;

/**
 * Activity Counter class
 * @author mgng
 */
class Counter extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		if (!$this->_is_login) {
			Server::send404Header("404 Not Found");
			return false;
		}

		$DaoActivity = new DaoActivity();
		$result = $DaoActivity->getCount();
		if ( ! $result["status"]) {
			Server::send404Header("system error.");
			return false;
		}
		$counter_list = $result["data"];

		header("Content-Type:application/json; charset=UTF-8");
		echo json_encode($counter_list);

		return true;
	}

}
