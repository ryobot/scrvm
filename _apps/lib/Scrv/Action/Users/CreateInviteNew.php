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
		$DaoInvites = new DaoInvites();
		$result = $DaoInvites->createInvite($this->_login_user_data["id"], $hash, $this->_login_user_data["role"]);
		if ($result["status"]) {
			$result["data"] = array(
				"created_link" => Server::getFullHostUrl() . "{$this->_BasePath}Invites?hash={$hash}",
			);
		}

		header("Content-Type:application/json; charset=utf-8");
		echo json_encode($result);

		// tokenクリア
		$this->_Session->clear(Scrv\SessionKeys::CSRF_TOKEN);

		return true;
	}

}
