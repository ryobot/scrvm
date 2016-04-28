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

	/** ログインタイムアウト時間 */
	const LOGIN_TIMEOUT = "LOGIN_TIMEOUT";

	/** ログインタイムアウト時刻 */
	const LOGIN_EXPIRES = "LOGIN_EXPIRES";

	/** 汎用エラーメッセージ格納用 */
	const ERROR_MESSAGES = "ERROR_MESSAGES";

	/** 汎用POSTパラメータ格納用 */
	const POST_PARAMS = "POST_PARAMS";

	/** CSRF トークン確認用 */
	const CSRF_TOKEN = "CSRF_TOKEN";

	/** twitter oauth データ格納用 */
	const TWITTER_OAUTH = "TWITTER_OAUTH";

	/** twitter access token データ格納用 */
	const TWITTER_ACCESS_TOKEN = "TWITTER_ACCESS_TOKEN";

	/** 招待用データ格納用 */
	const INVITATIONS_DATA = "INVITATIONS_DATA";

	/** ログイン後に遷移させるURL */
	const URL_AFTER_LOGINED = "URL_AFTER_LOGINED";
}
