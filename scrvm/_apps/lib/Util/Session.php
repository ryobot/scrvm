<?php
/**
 * Session.php
 * @author mgng
 */

namespace lib\Util;

/**
 * セッション管理クラス
 * @author mgng
 * @package lib\Util
 */
class Session
{
	/**
	 * 自クラスインスタンス格納用
	 * @var \lib\Util\Session
	 */
	protected static $_self = null;

	/**
	 * 自クラスのインスタンスを取得する。
	 * @param array $setting セッション設定の連想配列
	 * @return \lib\Util\Session
	 */
	public static function getInstance(array $setting = array())
	{
		if (is_null(self::$_self)) {
			self::$_self = new self($setting);
		}
		return self::$_self;
	}

	/**
	 * コンストラクタ。セッション設定を行う。
	 * @param array $setting
	 * @return boolean
	 */
	public function __construct(array $setting = array())
	{
		foreach ($setting as $key => $value) {
			ini_set($key, $value);
		}
		// ini_set で設定できない項目は関数経由で設定
		if (isset($setting['session.cookie_lifetime'], $setting['session.cookie_path'])) {
			session_set_cookie_params($setting['session.cookie_lifetime'], $setting['session.cookie_path']);
		}
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

	/**
	 * セッションを開始する
	 * @return boolean
	 */
	public function start()
	{
		if (!isset($_SESSION)) {
			session_start();
		}
		return true;
	}

	/**
	 * セッションIDを返す
	 * @return string|null
	 */
	public function id()
	{
		return isset($_SESSION) ? session_id() : null;
	}

	/**
	 * 現在のセッション名を返す。
	 * @return string|null
	 */
	public function name()
	{
		return isset($_SESSION) ? session_name() : null;
	}

	/**
	 * セッションに値をセットする
	 * @param string $key セッションキー名
	 * @param mixed $value セットする値
	 * @return boolean
	 */
	public function set($key, $value)
	{
		if (isset($_SESSION)) {
			$_SESSION[$key] = $value;
		}
		return true;
	}

	/**
	 * セッションから値を取り出す。設定されていない場合はnullを返す。
	 * @param string $key セッションキー名
	 * @return mixied|null
	 */
	public function get($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	/**
	 * sessionID を変更する。ログイン後などに実行すること。
	 * @return boolean
	 */
	public function regenerate()
	{
		if (isset($_SESSION)) {
			session_regenerate_id(true);
		}
		return true;
	}

	/**
	 * セッション値をクリアする。
	 * @param string $key セッションキー名
	 * @return boolean
	 */
	public function clear($key)
	{
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
		return true;
	}

	/**
	 * セッションを初期化する
	 * @return boolean
	 */
	public function init()
	{
		if (isset($_SESSION)) {
			$_SESSION = array();
		}
		return true;
	}

	/**
	 * セッションを破棄しセッションIDも変更する。
	 * @return boolean
	 */
	public function destroy()
	{
		if (isset($_SESSION)) {
			$this->regenerate();
			session_destroy();
		}
		return true;
	}
}
