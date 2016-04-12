<?php
/**
 * Validate.php
 * @author tomita
 */

namespace lib\Util;

/**
 * Validation系クラス
 * @author tomita
 * @package lib\Util
 */
class Validate
{

	/**
	 * $paramが空文字でない場合true、それ以外はfalseを返す。
	 * @param string $param 検証する文字列
	 * @return boolean
	 */
	public function isNotEmpty($param)
	{
		return $param !== "";
	}

	/**
	 * $paramが妥当なメールアドレスの場合trueを、それ以外はfalseを返す。
	 * メールアドレスのバリデーション仕様は PHP の FILTER_VALIDATE_EMAIL に準拠。
	 * @param string $param 検証する文字列
	 * @return boolean
	 */
	public function isValidEmail($param)
	{
		if ( filter_var($param, FILTER_VALIDATE_EMAIL) === false ) {
			return false;
		}
		return true;
	}

	/**
	 * $paramが半角英(大文字小文字)数混在、指定文字数以上の場合true,それ以外はfalseを返す。
	 * @param string $param 検証する文字列
	 * @param integer $length 必要な文字数
	 * @return boolean
	 */
	public function isValidPassword($param, $length = 8)
	{
		if (mb_strlen($param) < $length
			|| preg_match("/[a-z]/", $param) !== 1
			|| preg_match("/[A-Z]/", $param) !== 1
			|| preg_match("/[0-9]/", $param) !== 1
		) {
			return false;
		}
		return true;
	}

	/**
	 * $param が $length 以内ならtrue,それ以外はfalseを返す
	 * @param string $param 検証する文字列
	 * @param integer $length 許可する文字数
	 * @return boolean
	 */
	public function isWithin($param, $length)
	{
		return mb_strlen($param) <= $length ;
	}

	/**
	 * 電話番後チェック。
	 * $is_hyphen が true の場合、$param が 数字-数字-数字 の形式であればtrue,
	 * $is_hyphen が falseの場合、$param が 数字だけで構成されていればtrue,
	 * それ以外はfalseを返す。
	 * @param string $param 検証する文字列
	 * @param boolean $is_hyphen ハイフンを許可するかどうか。default false
	 * @return boolean
	 */
	public function isTel($param, $is_hyphen = false)
	{
		if ($is_hyphen === true) {
			return preg_match( "/\A\d+\-\d+\-\d+\z/", $param ) === 1;
		}
		return preg_match( "/\A\d+\z/", $param ) === 1;
	}

	/**
	 * $param が全て全角カタカナであればtrue、そうでなければfalseを返す。
	 * @param string $param 検証する文字列
	 * @return boolean
	 */
	public function isKatakana($param)
	{
		return preg_match("/\A[ァ-ヾ]+\z/u", $param) === 1;
	}

	/**
	 * $param が $pattern に一致したらtrue,それ以外はfalseを返す
	 * @param string $param 検証する文字列
	 * @param string $pattern 一致させたい正規表現
	 * @return boolean
	 */
	public function isMatch($param, $pattern)
	{
		return preg_match($pattern, $param) === 1;
	}

}
