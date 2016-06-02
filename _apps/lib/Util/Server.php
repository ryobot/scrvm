<?php
/**
 * Server.php
 * @author mgng
 */

namespace lib\Util;

/**
 * サーバ変数関連クラス
 * @author mgng
 * @package lib\Util
 */
class Server
{

	/**
	 * GET値を取得する。未定義だった場合は $default の値を返す。
	 * @param string $key GETパラメータ名
	 * @param mixed $default 未定義の場合に返す値。default null
	 * @return mixed
	 */
	public static function get($key, $default=null)
	{
//		$value = filter_input(INPUT_GET, $key);
		$value = isset($_GET[$key]) ? $_GET[$key] : null;
		if ($value === null) {
			return $default;
		}
		return self::sanituzeNulChar($value);
	}

	/**
	 * GET配列値取得用。未定義または配列以外だった場合は $default の値を返す。
	 * @param string $key GETパラメータ名
	 * @param mixed $default 未定義の場合に返す値。default null
	 * @return mixed
	 */
	public static function getArray($key, $default=null)
	{
		if ( ! isset($_GET[$key]) ) {
			return $default;
		}
		$items = $_GET[$key];
		if ( !is_array($items) ) {
			return $default;
		}
		foreach( $items as $k => $val ) {
			if ( !is_string($val) ) {
				unset($items[$k]);
			}
		}
		return self::sanituzeNulChar($items);
	}

	/**
	 * POST値取得。未定義だった場合は $default の値を返す。
	 * @param string $key POSTパラメータ名
	 * @param mixed $default 未定義の場合に返す値。default null
	 * @return mixed
	 */
	public static function post($key, $default=null)
	{
		$value = filter_input(INPUT_POST, $key);
		if ($value === null) {
			return $default;
		}
		return self::sanituzeNulChar($value);
	}

	/**
	 * POST配列値取得用。未定義または配列以外だった場合は $default の値を返す。
	 * @param string $key POSTパラメータ名
	 * @param mixed $default 未定義の場合に返す値。default null
	 * @return mixed
	 */
	public static function postArray($key, $default=null)
	{
		if ( ! isset($_POST[$key]) ) {
			return $default;
		}
		$items = $_POST[$key];
		if ( !is_array($items) ) {
			return $default;
		}
		foreach( $items as $k => $val ) {
			if ( !is_string($val) ) {
				unset($items[$k]);
			}
		}
		return self::sanituzeNulChar($items);
	}

	/**
	 * $key に指定した環境変数を取得する。
	 * @param string $key 取得するキー名
	 * @return null|string
	 */
	public static function env($key)
	{
		$value_env = filter_input(INPUT_ENV, $key);
		if ($value_env !== null) {
			return $value_env;
		}
		$value_server = filter_input(INPUT_SERVER, $key);
		if ($value_server !== null) {
			return $value_server;
		}
		return null;
	}

	/**
	 * $valueに含まれる nullバイト文字列を削除する。
	 * @param string|array $value 対象文字列
	 * @return string
	 */
	public static function sanituzeNulChar($value)
	{
		if ( is_array( $value ) ) {
			 array_map( array( 'self', 'sanituzeNulChar' ), $value);
		}
		return str_replace(pack('x'), '', $value);
	}

	/**
	 * 指定パスにリダイレクトする。
	 * @param string $path ホスト名以降のパス。例: /index.php, /path/to/xxx.php
	 * @return boolean
	 */
	public static function redirect($path)
	{
		$location = self::getFullHostUrl() . "{$path}";
		header("Location: {$location}");
		return true;
	}

	/**
	 * 自サーバのフルURIを返す。
	 * @return string
	 */
	public static function getFullHostUrl()
	{
		$protocol = self::_getProtocol();
		$host = self::env('HTTP_HOST');
		return "{$protocol}://{$host}";
	}

	/**
	 * get protocol
	 * @return string
	 */
	private static function _getProtocol()
	{
		$is_https = self::env('HTTPS');
		$port = self::env('SERVER_PORT');
    if (isset($is_https) && strtolower($is_https) == 'on') {
      return 'https';
    } elseif (isset($port) && ($port == '443')) {
      return 'https';
    }
		return 'http';
	}

	/**
	 * self url
	 * @param boolean $dropqs
	 * @return string
	 */
	public static function selfUrl($dropqs = true)
	{
		$protocol = self::_getProtocol();
		$url = sprintf('%s://%s%s', $protocol, self::env('SERVER_NAME'), self::env('REQUEST_URI'));
		$parts = parse_url($url);

		$scheme = $parts['scheme'];
		$host = $parts['host'];
		$path = isset($parts['path']) ? $parts['path'] : "";
		$qs   = isset($parts['query']) ? $parts['query'] : "";
		$port = self::env('SERVER_PORT');

		if ( ($scheme === 'https' && $port !== '443')
			|| ($scheme == 'http' && $port !== '80')
		) {
			$host = "{$host}:{$port}";
		}
		$last_url = "{$scheme}://{$host}{$path}";
		if ( ! $dropqs) {
			return "{$last_url}?{$qs}";
		}
    return $last_url;
	}

	/**
	 * 404 NotFound ヘッダを出力する。
	 * **このメソッドの前に出力があるとエラーになるので注意!**
	 * @param string $contents ヘッダ出力後に表示する文字列。default 空文字
	 * @return boolean
	 */
	public static function send404Header($contents = "")
	{
		header('HTTP', true, 404);
		echo $contents;
		return true;
	}

	/**
	 * 汎用的なファイルダウンロード用HTTPヘッダを出力する。
	 * @param string $filename ダウンロードファイル名
	 * @param integer $filesize ファイルサイズ default null
	 * @return boolean
	 */
	public static function sendDownloadHeaders($filename, $filesize = null)
	{
		header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate");
    header("Pragma: public");
		if ( $filesize !== null ) {
			header("Content-Length: {$filesize}");
		}
		return true;
	}

}
