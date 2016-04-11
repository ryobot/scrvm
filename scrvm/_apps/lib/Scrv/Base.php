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
	protected $_common_ini = array();

	/**
	 * スクリプト開始時のタイムスタンプ
	 * @var type
	 */
	protected $_nowTimestamp = null;

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
		// 共通設定ファイル読み込み処理
		$ini_file = $this->getConfigDir() . "common.ini";
		if ( ! is_readable($ini_file) ) {
			echo "config load error.";
			exit;
		}
		$this->_common_ini = parse_ini_file( $ini_file, true );

		// 現在タイムスタンプ取得
		$this->_nowTimestamp = time();

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
