<?php
/**
 * lib/Scrv/Router.php
 * @author mgng
 */

namespace lib\Scrv;

/**
 * router class
 * @author mgng
 * @package lib\Scrv
 */
class Router
{
	/**
	 * $action と $run に応じたクラスインスタンスを返す。
	 * クラスインスタンスが存在しなければ null を返す。
	 * @param string $action
	 * @param string $run
	 * @return class instatce | null
	 */
	public function getInstance( $action, $run )
	{
		$instance = null;
		$pattern = "/\A[a-zA-Z0-9_]+\z/";
		if ( preg_match($pattern, $action) !== 1 || preg_match($pattern, $run) !== 1 ) {
			return $instance;
		}
		$run_class_name = "\\lib\\Scrv\\Action\\{$action}\\{$run}";
		if (class_exists($run_class_name) ) {
			$instance = new $run_class_name;
		}
		return $instance;
	}

}
