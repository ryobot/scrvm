<?php
/**
 * /lib/Scrv/Action/Albums/EditRun.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Util\Server as Server;
use lib\Util\File as File;
use lib\Util\Images as Images;

/**
 * albums Edit Run class
 * @author mgng
 */
class EditRun extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはログイン画面へ
		$this->isNotLogined($this->_BasePath . "Auth");

		// 各パラメータ取得, mb_trim, 改行コード統一
		$post_params = array(
			"id" => Server::post("id", ""),
			"token" => Server::post("token", ""),
			"artist" => Server::post("artist", ""),
			"title" => Server::post("title", ""),
			"year" => Server::post("year", ""),
		);
		foreach( $post_params as &$val ) {
			$val = convertEOL(mb_trim($val), "\n");
		}
		// tracks は jsonでわたってくる
		$tracks = Server::postArray("tracks", array());
		foreach( $tracks as &$track ) {
			$track = json_decode( convertEOL(mb_trim($track), "\n") );
		}

		// sess_tokenチェック
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( ! isset($sess_token) || $sess_token !== "{$post_params["token"]}:{$post_params["id"]}" ) {
			Server::send404Header("404 not found");
			return false;
		}

		// post_params チェック
		$check_result = $this->_checkPostParams($post_params, $tracks);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Albums/Edit/id/{$post_params["id"]}");
			return false;
		}

		// ファイルアップロード チェック
		$upload_result = $this->_checkFileUpload("file", $this->_login_user_data["id"]);
		if ( ! $upload_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $upload_result["messages"]);
			Server::redirect($this->_BasePath . "Albums/Edit/id/{$post_params["id"]}");
			return false;
		}
		$img_file = isset($upload_result["data"]["img_path"]) ? $upload_result["data"]["img_path"] : null;

		// save
		$DaoAlbums = new DaoAlbums();
		$save_result = $DaoAlbums->save(
			(int)$post_params["id"],
			$post_params["artist"],
			$post_params["title"],
			$post_params["year"],
			$img_file,
			$tracks,
			$this->_login_user_data["id"]
		);
		if ( !$save_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $save_result["messages"]);
			Server::redirect($this->_BasePath . "Albums/Edit/id/{$post_params["id"]}");
			return false;
		}

		// 編集ページにリダイレクト
		$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, array("保存しました。"));
		Server::redirect($this->_BasePath . "Albums/Edit/id/{$post_params["id"]}");
		return true;
	}

	/**
	 * post params check
	 * @param array $post_params
	 * @param array $tracks 各配列は stdObject
	 * @return type
	 */
	private function _checkPostParams(array $post_params, array $tracks)
	{
		$check_result = getResultSet();

		// post_params
		if ( $post_params["artist"] === "" ) {
			$check_result["messages"]["artist"] = "artist が未入力です。";
		} else if ( mb_strlen($post_params["artist"]) > 50 ){
			$check_result["messages"]["artist"] = "artist は50文字以内で入力してください。";
		}
		if ( $post_params["year"] !== ""
			&& ( mb_strlen($post_params["year"]) > 4 || !ctype_digit($post_params["year"]) )
		){
			$check_result["messages"]["year"] = "year は4文字以内の数値で入力してください。";
		}

		// tracks
		foreach($tracks as $idx => $track) {
			$num = $idx + 1;

			if ( ! isset( $track->id, $track->track_title ) ) {
				$check_result["messages"]["track_{$num}"] = "tr.{$num} が不正です。";
				break;
			}
			if ( $track->track_title === "" ) {
				$check_result["messages"]["track_{$num}"] = "tr.{$num} が未入力です。";
			} else if (mb_strlen($track->track_title) > 100 ) {
				$check_result["messages"]["track_{$num}"] = "tr.{$num} は100文字以内で入力してください。";
			}
		}
		if (count($tracks) === 0) {
			$check_result["messages"]["track_none"] = "track は必ず1曲必要です。";
		}

		$check_result["status"] = count($check_result["messages"]) === 0;
		return $check_result;
	}

	/**
	 * ファイルアップロード check
	 * @param string $input_key フォームのname属性
	 * @return boolean
	 */
	private function _checkFileUpload($input_key)
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
		$Images = new Images();
		$imagesize = $Images->checkType($tmp_name, array("jpeg","png","gif"));
		if ( $imagesize === false ) {
			$result["messages"]["file"] = "ファイル形式エラーです。";
			return $result;
		}
		$ext = str_replace("image/", "", $imagesize["mime"]);

		// ファイル作成, 長い方の辺 300px にする
		$img_file = sha1($tmp_name . mt_rand(10000000, 99999999)) . ".{$ext}";
		$dir = substr($img_file, 0,4);
		$img_path = substr($img_file, 4, strlen($img_file));
		$sep = "/";
		$subdir = implode($sep, preg_split('//', $dir, -1, PREG_SPLIT_NO_EMPTY)) . $sep;
		$dir_path = __DIR__ . "/../../../../../files/covers/{$subdir}";
		// dir がない場合は作成
		if (! file_exists($dir_path) && ! mkdir($dir_path, 0777, true) ) {
			$result["messages"]["file"] = "ディレクトリの作成に失敗しました。";
			return $result;
		}
		// 画像を移動
		if ( ! move_uploaded_file( $tmp_name, $dir_path.$img_path) ) {
			$result["messages"]["file"] = "ファイルの移動に失敗しました。";
			return $result;
		}
		// 300px に縮小
		$Images->makeThumbnail($dir_path.$img_path, $dir_path.$img_path, 300, 300, true);

		$result["status"] = true;
		$result["data"]["img_path"] = $subdir.$img_path;
		return $result;
	}
}
