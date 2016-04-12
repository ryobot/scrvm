<?php
/**
 * /lib/Scrv/Action/Users/Save.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;
use lib\Util\File as File;
use lib\Util\Images as Images;

/**
 * Users Save class
 * @author mgng
 */
class Save extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインの場合はログイン画面にリダイレクト
		$this->isNotLogined($this->_BasePath . "Auth");

		// POSTパラメータ取得、mb_trim、改行コード統一、セッション保持
		$post_params = array(
			"token" => Server::post("token", ""),
			"username" => Server::post("username", ""),
			"password" => Server::post("password", ""),
			"user_id" => Server::post("user_id", ""),
		);
		foreach( $post_params as &$val ) {
			$val = convertEOL(mb_trim($val), "\n");
		}
		$this->_Session->set(Scrv\SessionKeys::POST_PARAMS, $post_params);

		// 管理者以外でセッションのユーザIDとPOST user_id が一致しなかったらエラー
		if ( $this->_login_user_data["role"] !== "admin"
			&& $post_params["user_id"] !== (string)$this->_login_user_data["id"]
		) {
			Server::send404Header("system error.");
			return false;
		}

		// CSRFチェック
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( $sess_token !== $post_params["token"] ) {
			Server::send404Header("system error..");
			return false;
		}

		// post_params チェック
		$check_result = $this->_checkPostParams($post_params);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Users/Edit?id=" . $post_params["user_id"]);
			return false;
		}

		// ファイルアップロード チェック
		$upload_result = $this->_checkFileUpload("file", (int)$post_params["user_id"]);
		if ( ! $upload_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $upload_result["messages"]);
			Server::redirect($this->_BasePath . "Users/Edit?id=" . $post_params["user_id"]);
			return false;
		}

		// 登録処理
		$DaoUsers = new DaoUsers();
		$save_result = $DaoUsers->save(
			(int)$post_params["user_id"],
			$post_params["username"],
			$post_params["password"],
			isset( $upload_result["data"]["img_file"] ) ? $upload_result["data"]["img_file"] : null
		);
		if ( ! $save_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $save_result["messages"]);
			Server::redirect($this->_BasePath . "Users/Edit?id=" . $post_params["user_id"]);
			return false;
		}

		// セッションのpost_param をクリアしてリダイレクト
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);
		Server::redirect($this->_BasePath . "Users/Edit?id=" . $post_params["user_id"]);
		return true;
	}

	/**
	 * post params check
	 * @param array $post_params
	 * @return resultSet
	 */
	private function _checkPostParams(array $post_params)
	{
		$check_result = getResultSet();

		if ( $post_params["username"] === "" ) {
			$check_result["messages"]["username"] = "username が未入力です。";
		} else if ( mb_strlen($post_params["username"]) > 50 ){
			$check_result["messages"]["username"] = "username は50文字以内で入力してください。";
		}

		if ( $post_params["password"] === "" ) {
			$check_result["messages"]["password"] = "password が未入力です。";
		} else if ( mb_strlen($post_params["password"]) > 100 ){
			$check_result["messages"]["password"] = "password は100文字以内で入力してください";
		}

		$check_result["status"] = count($check_result["messages"]) === 0;
		return $check_result;
	}

	/**
	 * ファイルアップロード check
	 * @param string $input_key フォームのname属性
	 * @param integer $user_id
	 * @return boolean
	 */
	private function _checkFileUpload($input_key, $user_id)
	{
		$result = getResultSet();

		// アップロードされない場合は何もしない
		if ( ! isset( $_FILES, $_FILES[$input_key] )
			|| $_FILES[$input_key]["error"] === UPLOAD_ERR_NO_FILE
		) {
			$result["status"] = true;
			return $result;
		}

		// ファイルアップロードエラー時処理
		$error_message = File::getUploadErrorMessage($_FILES[$input_key]["error"]);
		if ( $error_message !== "" ) {
			$result["messages"]["file"] = $error_message;
			return $result;
		}

		// tmp領域にアップロードされたファイルパス
		$tmp_name = $_FILES[$input_key]["tmp_name"];

		// 画像形式チェック
		$imagesize = getimagesize($tmp_name);
		if ( ! isset($imagesize["mime"]) || preg_match("/\Aimage\/(jpeg|png|gif)\z/", $imagesize["mime"]) !== 1 ) {
			$result["messages"]["file"] = "ファイル形式エラーです。";
			return $result;
		}
		$ext = str_replace("image/", "", $imagesize["mime"]);

		// 格納先ディレクトリ作成
		$photo_dir = __DIR__ . "/../../../../../files/attachment/photo/{$user_id}/";
		if ( ! file_exists($photo_dir) && ! mkdir($photo_dir, 0777) ) {
			$result["messages"]["file"] = "ディレクトリの作成に失敗しました。";
			return $result;
		}

		// 画像を移動
		$to_file_path = "{$photo_dir}user.{$ext}";
		if ( ! move_uploaded_file( $tmp_name, $to_file_path) ) {
			$result["messages"]["file"] = "ファイルの移動に失敗しました。";
			return $result;
		}

		// サムネイル作成
		$src_w = $imagesize[0];
		$src_h = $imagesize[1];
		$resize_80 = $this->_getAutoSize($src_w, $src_h, 80, 80);
		$resize_150 = $this->_getAutoSize($src_w, $src_h, 150, 150);
		$Images = new Images();
		$Images->makeThumbnail($to_file_path, "{$photo_dir}thumb80_user.{$ext}", $resize_80["width"], $resize_80["height"]);
		$Images->makeThumbnail($to_file_path, "{$photo_dir}thumb150_user.{$ext}", $resize_150["width"], $resize_150["height"]);

		$result["status"] = true;
		$result["data"]["img_file"] = "{$user_id}/user.{$ext}";
		return $result;
	}

	/**
	 * 縦横幅を自動調整したwdth,heightを返す
	 * @param integer $src_w 元ファイル幅
	 * @param integer $src_h 元ファイル高さ
	 * @param integer $resize_w リサイズ用幅
	 * @param integer $resize_h リサイズ用高さ
	 * @return array
	 */
	private function _getAutoSize($src_w, $src_h, $resize_w, $resize_h)
	{
		$per = ( $resize_w <= $resize_h ) ? ( $resize_h / $src_h ) : ( $resize_w / $src_w );
		return array(
			"width" => ceil( $src_w * $per ),
			"height" => ceil( $src_h * $per ),
		);
	}

}
