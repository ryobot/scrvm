<?php
/**
 * lib/Scrv/Action/Base.php
 * @author mgng
 */

namespace lib\Scrv\Action;
use lib\Scrv as Scrv;
use lib\Util\Session as Session;
use lib\Util\Server as Server;
use lib\Util\Template as Template;

/**
 * Scrv action Base class
 * @author mgng
 * @package lib\Scrv\Action
 */
class Base extends Scrv\Base
{
	/**
	 * セッションオブジェクト
	 * @var object
	 */
	protected $_Session = null;

	/**
	 * テンプレートオブジェクト
	 * @var object
	 */
	protected $_Template = null;

	/**
	 * ロガーオブジェクト
	 * @var object
	 */
	protected $_Logger = null;

	/**
	 * 基底タイトル格納変数
	 * @var string
	 */
	protected $_BaseTitle = null;

	/**
	 * 基底ディレクトリ格納変数
	 * @var string
	 */
	protected $_BasePath = null;

	/**
	 * ログイン済みフラグ格納用
	 * @var boolean
	 */
	protected $_is_login = false;

	/**
	 * ログインユーザの情報格納用連想配列
	 * @var array
	 */
	protected $_login_user_data = array();

	/**
	 * おすすめバナー広告連想配列
	 * @var array
	 */
	protected $_recommend_data = array();

	/**
	 * コンストラクタ
	 * セッションを開始、テンプレートインスタンスを生成する。
	 * @return boolean
	 */
	public function __construct()
	{
		parent::__construct();

		// セッション開始
		$this->_Session = new Session( self::$_common_ini["session"] );
		$this->_Session->start();

		// テンプレートインスタンス生成
		$this->_Template = new Template();
		$this->_Template->template_dir = __DIR__ . "/../../../" . self::$_common_ini["common"]["template_dir"];

		// 基底関連
		$this->_BaseTitle = self::$_common_ini["common"]["base_title"];
		$this->_BasePath = self::$_common_ini["common"]["base_path"];

		// ログイン関連
		// ログイン時は有効時間チェック
		$is_login = $this->_Session->get(Scrv\SessionKeys::IS_LOGIN) === true;
		if ( $is_login ) {
			// 有効時間を超えていたらセッション破棄(ログアウト)
			$expires = $this->_Session->get(Scrv\SessionKeys::LOGIN_EXPIRES);
			if ( $expires < self::$_nowTimestamp ) {
				$this->_Session->init();
				$this->_Session->destroy();
			} else {
				$timeout = $this->_Session->get(Scrv\SessionKeys::LOGIN_TIMEOUT);
				$this->_Session->set(Scrv\SessionKeys::LOGIN_EXPIRES, self::$_nowTimestamp + $timeout);
			}
		}
		$this->_is_login = $is_login;
		$this->_login_user_data = $this->_Session->get(Scrv\SessionKeys::LOGIN_USER_DATA);

		// テンプレートに埋め込んでおく
		$this->_Template->assign(array(
			"base_title" => $this->_BaseTitle,
			"base_path" => $this->_BasePath,
			"now_timestamp" => self::$_nowTimestamp,
			"is_login" => $this->_is_login,
			"login_user_data" => $this->_login_user_data,
		));
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
	 * 実装メソッド
	 */
	public function run(){}

	/**
	 * ログイン済みであれば $redirect_path にリダイレクトして処理を終了する。
	 * @param string $redirect_path リダイレクト先パス文字列
	 * @return boolean
	 */
	public function isLogined( $redirect_path )
	{
		if ( $this->_is_login ) {
			Server::redirect( $redirect_path );
			exit;
		}
		return true;
	}

	/**
	 * 未ログインであれば $redirect_path にリダイレクトして処理を終了する。
	 * @param string $redirect_path リダイレクト先パス文字列
	 * @return boolean
	 */
	public function isNotLogined( $redirect_path )
	{
		if ( ! $this->_is_login ) {
			Server::redirect( $redirect_path );
			exit;
		}
		return true;
	}

}

