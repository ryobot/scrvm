<?php
/**
 * /lib/Scrv/Action/Albums/Add.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;

/**
 * albums add class
 * @author mgng
 */
class Add extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはログイン画面へ
		$this->isNotLogined($this->_BasePath . "Auth");

		$this->_Template->assign(array())->display("Albums/Add.tpl.php");
		return true;
	}
}
