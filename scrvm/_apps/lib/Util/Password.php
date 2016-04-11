<?php
/**
 * Pasword.php
 * @author mgng
 */

namespace lib\Util;

/**
 * Password class
 * @author mgng
 * @package lib\Util
 */
class Password
{
	/**
	 * $seed からランダムなハッシュを作成して返す
	 * @param string $seed
	 * @return string
	 */
	public function makeRandomHash($seed)
	{
		$rand = mt_rand(10000000,99999999);
		return $this->makePasswordHash($seed, "{$rand}{$seed}", $seed, 100);
	}

	/**
	 * パスワード等の文字列を安全にハッシュ化する。
	 * @param string $user_id ユーザID文字列
	 * @param string $password パスワード文字列
	 * @param string $salt_key 固有のサルトキー
	 * @param int $stretch_count 伸張回数。default 1000回
	 * @return string
	 */
	public function makePasswordHash($user_id, $password, $salt_key, $stretch_count = 1000)
	{
		$salt = sha1( $user_id . $salt_key );
		$hash = '';
		for ( $i=0; $i<$stretch_count; $i++ ) {
			$hash = sha1( "{$hash}{$password}{$salt}" );
		}
		return $hash;
	}

}
