<?php
/**
 * /lib/Scrv/Action/Reviews/Edit.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Scrv\Helper\Reviews\SituationList as SituationList;
use lib\Util\Server as Server;
use lib\Util\Password as Password;

/**
 * Reviews Edit class
 * @author mgng
 */
class Edit extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはログイン画面へ飛ばす
		$this->isNotLogined($this->_BasePath . "Auth");

		$review_id = Server::get("id");
		if ( !isset($review_id) || !ctype_digit($review_id) ) {
			Server::send404Header("not found.");
			return false;
		}

		// レビュー検索
		$DaoReviews = new DaoReviews();
		$review_result = $DaoReviews->review($review_id);
		if (!$review_result["status"] ) {
			Server::send404Header("not found..");
			print_r($review_result);
			return false;
		}
		$album_id = $review_result["data"]["album_id"];

		// アルバム検索
		$DaoAlbums = new DaoAlbums();
		$album_result = $DaoAlbums->view($album_id);
		if (!$album_result["status"]){
			Server::send404Header("not found...");
			return false;
		}

		// セッション値取得
		$post_params = $this->_Session->get(Scrv\SessionKeys::POST_PARAMS);
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// POST params がなければ DBの値
		if ( !isset($post_params) || !isset($post_params["review_id"]) ) {
			$post_params = $review_result["data"];
		}

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		// situasion list 取得
		$SituationList = new SituationList();
		$situation_list = $SituationList->getList();

		$this->_Template->assign(array(
			"token" => $token,
			"album_id" => $album_id,
			"review_id" => $review_id,
			"album" => $album_result["data"]["album"],
			"tracks" => $album_result["data"]["tracks"],
			"reviews" => $album_result["data"]["reviews"],
			"situation_list" => $situation_list,
			"post_params" => $post_params,
			"error_messages" => $error_messages,
		))->display("Reviews/Edit.tpl.php");
		return true;
	}
}
