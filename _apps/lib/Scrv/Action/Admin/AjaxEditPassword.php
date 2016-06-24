<?php
/**
 * /lib/Scrv/Action/Admin/AjaxEditPassword.php
 * @author mgng
 */

namespace lib\Scrv\Action\Admin;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Admin as DaoAdmin;
use lib\Util\Server as Server;

/**
 * AjaxEditPassword
 * @author mgng
 */
class AjaxEditPassword extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインまたは 管理者以外は404で終了
		if ( !$this->_is_login || $this->_login_user_data["role"] !== "admin" ) {
			Server::send404Header("404 not found");
			return false;
		}

		// postパラメータ取得
		$id = Server::post("id");
		$username = Server::post("username");
		$password = Server::post("password");
		$password_new = Server::post("password_new");

		// 返却用オブジェクト
		$resultSet = getResultSet();

		// jsonヘッダ出力
		header("Content-Type:application/json; charset=utf-8");

		// validation
		if ( ! isset( $id, $username, $password, $password_new ) ) {
			$resultSet["messages"][] = "不正なアクセスです。";
			echo json_encode($resultSet);
			return false;
		}

		// 更新処理
		$DaoAdmin = new DaoAdmin();
		$resultSet = $DaoAdmin->savePassword($id, $username, $password, $password_new);
		echo json_encode($resultSet);
		return true;
	}

}
