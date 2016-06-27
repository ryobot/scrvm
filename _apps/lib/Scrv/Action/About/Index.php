<?php
/**
 * /lib/Scrv/Action/About/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\About;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;

/**
 * About Index class
 * @author mgng
 */
class Index extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		$this->_Template->assign(array())->display("About/Index.tpl.php");
		return true;
	}

}
