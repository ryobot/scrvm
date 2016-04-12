<?php
/**
 * lib/Scrv/SessionKeys.php
 * @author tomita
 */

namespace lib\Scrv;

/**
 * セッションキーアクセサ定義
 * @author mgng
 * @package lib\Scrv
 */
class SessionKeys
{
	/** ログイン済フラグ */
	const IS_LOGIN = "IS_LOGIN";

	/** ログインユーザ情報格納用 */
	const LOGIN_USER_DATA = "LOGIN_USER_DATA";

	/** ログインエラー時のメッセージ */
	const LOGIN_ERROR = "LOGIN_ERROR_MESSAGE";

	/** 汎用エラーメッセージ格納用 */
	const ERROR_MESSAGES = "ERROR_MESSAGES";

	/** 汎用POSTパラメータ格納用 */
	const POST_PARAMS = "POST_PARAMS";

	/** CSRF トークン確認用 */
	const CSRF_TOKEN = "CSRF_TOKEN";
}
