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
 * http_build_query shortcut
 * @param array $params
 * @return string
 */
function hbq(array $params)
{
	return http_build_query($params);
}

/**
 * path 最適化したものを返す
 * @param array $params
 * @param string $separator default "/"
 * @return string
 */
function hbq2(array $params, $separator = "/")
{
	$buf = array();
	foreach($params as $k => $v) {
		if ($v !== null && $v !== "") {
			$buf[] = "{$k}{$separator}" . urlencode($v);
		}
	}
	return implode($separator, $buf);
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

/**
 * twitter 風日付表示
 * @param string $date Y-m-d H:i:s
 * @return string
 */
function timeAgoInWords($date)
{
	$now = time();
	$time = strtotime($date);
	$format = date("Y", $now) !== date("Y", $time) ? 'Y年n月j日 G:i' : 'n月j日 G:i';
	return date($format,$time);
}

function linkIt($text, $target_blank = true)
{
	$pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/u';
	$target = $target_blank ? ' target="blank"' : "";
	$replacement = '<a href="\1"'.$target.'>\1</a>';
	return preg_replace($pattern, $replacement, $text);
}

function dateInWeek($date)
{
	$time = strtotime($date);
	$format = 'M. j';
                  $str = date($format,$time);
                  $day = date('D.', $time);
                  $str .= "<br>".$day;
                  if ( $day == "Sat." ) {
                        return "<font color=steelblue>".$str."</font>";
                  } else if ( $day == "Sun." ) {
                        return "<font color=red>".$str."</font>";
                  } else if ( $day == "Mon." ) {
                       return "<font color=gold>".$str."</font>";
                  } else if ( $day == "Tue." ) {
                        return "<font color=orange>".$str."</font>";
                  } else if ( $day == "Wed." ) {
                        return "<font color=seagreen>".$str."</font>";
                  } else if ( $day == "Thu." ) {
                        return "<font color=brown>".$str."</font>";
                  } else if ( $day == "Fri." ) {
                        return "<font color=violet>".$str."</font>";
                  }
                  return $str;
}
