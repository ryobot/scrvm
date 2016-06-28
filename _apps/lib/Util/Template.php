<?php
/**
 * Template.php
 * @author tomita
 */

namespace lib\Util;

/**
 * テンプレートクラス
 * @author tomita
 * @package lib\Util
 */
class Template
{
	/**
	 * テンプレートディレクトリ
	 * @var string
	 */
	public $template_dir = "";

	/**
	 * テンプレートキャッシュディレクトリ
	 * @var string
	 */
	public $cache_dir = "";

	/**
	 * アサイン変数
	 * @var array
	 */
	protected $_assign_data = array();

	/**
	 * キャッシュ設定
	 * @var array
	 */
	protected $_cache_setting = array();

	/**
	 * コンストラクタ
	 * @return boolean
	 */
	public function __construct()
	{
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
	 * キャッシュ設定
	 * @param array $setting
	 * @return \lib\Util\Template
	 */
	public function setCache(array $setting=array())
	{
		$this->_cache_setting = $setting;
		return $this;
	}

	/**
	 * キャッシュを取得する
	 * @param type $request_uri
	 * @return type
	 */
	public function getCache($request_uri)
	{
		$cache_file_path = $this->_makeCacheFilePath($request_uri);
		// キャッシュファイルが存在しない、読み込めない
		if ( ! is_file($cache_file_path) || ! is_readable($cache_file_path) ) {
			return null;
		}
		$cache = unserialize(file_get_contents($cache_file_path));
		// シリアライズ失敗
		if ( $cache === false ) {
			return null;
		}
		// キャッシュライフタイムを超えている
		$now = time();
		if ( $now > $cache["created"] + $cache["expire"] ) {
			return null;
		}
		// キャッシュを返す
		return $cache["contents"];
	}

	/**
	 * get Etag
	 * @param string $string
	 * @return string
	 */
	public function getEtag($string)
	{
		return sha1($string);
	}

	/**
	 * キャッシュを生成する
	 * @param string $contents
	 * @return null
	 */
	private function _makeCache($contents)
	{
		if ( count($this->_cache_setting) === 0 ) {
			return null;
		}
		$seri = serialize(array_merge($this->_cache_setting, array("contents" => $contents)));
		$cache_file_path = $this->_makeCacheFilePath($this->_cache_setting["request_uri"]);
		// 最初にディレクトリを作成、失敗したら何もしない
		if ( ! is_dir(dirname($cache_file_path)) && ! mkdir(dirname($cache_file_path), 0777, true) ) {
			return null;
		}
		file_put_contents( $cache_file_path, $seri );
		return null;
	}

	/**
	 * リクエストURIから生成したキャッシュファイルフルパスを返す
	 * @param string $request_uri
	 * @return string
	 */
	private function _makeCacheFilePath($request_uri)
	{
		// ディレクトリ部分を取得
		$dir = dirname($request_uri);
		// ..が続いたりは削除
		$dir = preg_replace("/\.\.\//", "", $dir);
		// パスを生成
		return $this->cache_dir . $dir . "/" . $this->getEtag($request_uri);
	}

	/**
	 * 変数をアサインする。
	 * すでに変数が設定されていた場合、元の配列と引数配列をマージ(array_merge)する。
	 * @param array $data テンプレートにアサインする連想配列 default 空配列
	 * @return \lib\Util\Template
	 */
	public function assign( array $data = array() )
	{
		$this->_assign_data = array_merge($this->_assign_data, $data);
		return $this;
	}

	/**
	 * アサインされている変数配列を返す
	 * @return array
	 */
	public function getAssignData()
	{
		return $this->_assign_data;
	}

	/**
	 * アサインデータ配列をクリアする。
	 * @return \lib\Util\Template
	 */
	public function clearAssignData()
	{
		$this->_assign_data = array();
		return $this;
	}

	/**
	 * テンプレートを出力する。$is_display = false にすると出力しない。
	 * @param string $template テンプレートファイル名
	 * @param boolean $is_display 標準出力する場合はtrue。default true
	 * @return string テンプレートに変数を埋め込んだ文字列
	 */
	public function display( $template, $is_display = true )
	{
		extract( $this->_assign_data );
		ob_start();
		require $this->template_dir . $template;
		$contents = ob_get_contents();
		ob_end_clean();
		$this->_makeCache($contents);
		if ( $is_display ) {
			echo $contents;
		}
		return $contents;
	}

}

