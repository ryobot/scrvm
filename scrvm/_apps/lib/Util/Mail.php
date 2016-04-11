<?php
/**
 * Mail.php
 * @author mgng
 */

namespace lib\Util;

/**
 * メール送信クラス
 * @author mgng
 * @package lib\Util
 */
class Mail
{
	/**
	 * メール送信処理
	 * @param type $from From句
	 * @param type $to 宛先
	 * @param type $subject タイトル
	 * @param type $message メッセージ
	 * @return boolean
	 */
	public function send($from, $to, $subject, $message)
	{
		$additional_headers = "From:{$from}\r\n";
		return mb_send_mail($to, $subject, $message, $additional_headers);
	}

}
