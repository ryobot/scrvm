<?php
/**
 * lib/Scrv/Base.php
 * @author mgng
 */

namespace lib\Scrv;

/**
 * Scrv Base class
 * @author mgng
 * @package lib\Scrv
 */
class Base
{
	/**
	 * 共通設定ini格納用連想配列
	 * @var array
	 */
	protected static $_common_ini = null;

	/**
	 * スクリプト開始時のタイムスタンプ
	 * @var type
	 */
	protected static $_nowTimestamp = null;

	/**
	 * 設定ファイル格納さきディレクトリパスを返す
	 * @return string
	 */
	public function getConfigDir()
	{
		return __DIR__ . "/../../config/";
	}

	/**
	 * コンストラクタ
	 * @return boolean
	 */
	public function __construct()
	{
		if ( isset( self::$_common_ini, self::$_nowTimestamp ) ) {
			return true;
		}
		$ini_file = $this->getConfigDir() . "common.ini";
		if ( ! is_readable($ini_file) ) {
			echo "config load error.";
			exit;
		}
		self::$_common_ini = parse_ini_file( $ini_file, true );
		self::$_nowTimestamp = time();
		return true;
	}

	/**
	 * デストラクタ
	 * @return boolean
	 */
	public function __destruct()
	{
		return true;
	}
}
