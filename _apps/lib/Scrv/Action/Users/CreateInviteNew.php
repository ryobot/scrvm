<?php
/**
 * /lib/Scrv/Action/Users/CreateInvite.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Invites as DaoInvites;
use lib\Util\Password as Password;
use lib\Util\Server as Server;

/**
 * CreateInvite class
 * @author mgng
 */
class CreateInviteNew extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		if (!$this->_is_login) {
			Server::send404Header("404 not found.");
			return false;
		}

		$token = Server::post("token");
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( !isset($token,$sess_token) || $token !== $sess_token ) {
			Server::send404Header("404 not found..");
			return false;
		}

		// 招待用リンク生成
		$Password = new Password();
		$hash = $Password->makeRandomHash($this->_Session->id() . mt_rand(10000000,99999999));

		// 登録
		$result = getResultSet();
		$DaoInvites = new DaoInvites();
		$user_result = $DaoInvites->createInvite($this->_login_user_data["id"], $hash, $this->_login_user_data["role"]);
		if ($user_result["status"]) {
			// ユーザ情報をセッション上書き
			$current_user_data = $user_result["data"];
			$this->_Session->set(Scrv\SessionKeys::LOGIN_USER_DATA, $current_user_data);
			$result["status"] = true;
			$result["data"] = array(
				"created_link" => Server::getFullHostUrl() . "{$this->_BasePath}Invites?hash={$hash}",
				"can_be_invited_count" => (int)$this->_common_ini["invites"]["max_invited_count"] - $current_user_data["invited_count"],
			);
		} else {
			$result["messages"] = $user_result["messages"];
		}

		header("Content-Type:application/json; charset=utf-8");
		echo json_encode($result);

		// tokenクリア
		$this->_Session->clear(Scrv\SessionKeys::CSRF_TOKEN);

		return true;
	}

}
