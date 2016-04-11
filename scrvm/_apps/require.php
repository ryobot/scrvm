<?php
/**
 * require.php
 * @author mgng
 */

/**
 * 共通関数定義ファイル
 * PSR-0準拠 autoload 実装、htmlspecialcharsショートカット等、
 * アプリケーション共通で参照する関数定義ファイル
 */

// オートローダ設定
spl_autoload_register(function ($_className) {
	$className = ltrim($_className, '\\');
	$fileName = '';
	$namespace = '';
	$sep = DIRECTORY_SEPARATOR;
	if (($lastNsPos = strripos($className, '\\'))) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName = str_replace('\\', $sep, $namespace) . $sep;
	}
	$fileName .= str_replace('_', $sep, $className) . '.php';
	$class_path = __DIR__ . $sep . $fileName;
	if (is_file($class_path)) {
		require_once $class_path;
	}
});

/**
 * htmlspecialchars のショートカット関数
 * @param string $str 対象文字列
 * @return string 変換後文字列
 */
function h( $str )
{
	return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

/**
 * 全角SPも含めたtrim
 * @param string $str
 * @return string
 */
function mb_trim($str)
{
	$chars = "[\\x0-\x20\x7f\xc2\xa0\xe3\x80\x80]";
	return preg_replace("/\A{$chars}++|{$chars}++\z/u", '', $str);
}

/**
 * 改行コードの統一
 * @param string $string
 * @param string $to 変換する改行コード ディフォルト \n
 * @return string
 */
function convertEOL($string, $to = "\n")
{
	return strtr($string, array(
		"\r\n" => $to,
		"\r" => $to,
		"\n" => $to,
	));
}

/**
 * 内部エンコーディングと外部エンコーディングの違いを吸収したhttp_build_queryのショートカット
 * @param array $params
 * @return string
 */
function hbq(array $params)
{
	$internal_enc = ini_get("mbstring.internal_encoding");
	$output_enc = ini_get("mbstring.http_output");
	if ( $internal_enc === $output_enc ) {
		return http_build_query($params);
	}
	$_tmp = array();
	foreach($params as $key=>$val){
		$_tmp[$key] = mb_convert_encoding($val, $output_enc, $internal_enc);
	}
	return http_build_query($_tmp);
}

/**
 * 返却値などで用いる以下フォーマットの結果セットのひな形を返す。
 *
 * [
 *   "status" => false,
 *   "messages" => [],
 *   "data" => [],
 * ]
 *
 * 結果セットの項目を追加したい場合は $add_array に指定する。
 *
 * @param array $add_array 追加配列 default 空配列
 * @return array resultSet配列
 */
function getResultSet( array $add_array = array() )
{
	return array_merge(array(
		"status" => false,
		"messages" => array(),
		"data" => array(),
	), $add_array);
}

