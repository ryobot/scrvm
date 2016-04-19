<?php
/**
 * /lib/Scrv/Action/Albums/AddRun.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;
use lib\Scrv\Dao\Albums as DaoAlbums;

/**
 * albums AddRun class
 * @author mgng
 */
class AddRun extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはnot found
		if ( ! $this->_is_login ) {
			Server::send404Header("404 not found");
			return false;
		}

		// POSTパラメータ
		$post_params = array(
			"artist" => Server::post("artist", ""),
			"title" => Server::post("title", ""),
			"year" => Server::post("year", ""),
			"img_url" => Server::post("img_url", ""),
		);
		foreach( $post_params as &$val ) {
			$val = convertEOL(mb_trim($val), "\n");
		}
		$tracks = Server::postArray("tracks");
		foreach( $tracks as &$track ) {
			$track= convertEOL(mb_trim($track), "\n");
		}

		// check
		if ( $post_params["artist"] === ""
			|| $post_params["title"] === ""
			|| count($tracks) === 0
			|| ($post_params["year"] !== "" && ! ctype_digit($post_params["year"]))
		) {
			Server::send404Header();
			return false;
		}

		// 画像が空文字じゃなくhttpで始まっている場合はgetcontents
		$img_file = "";
		if ( $post_params["img_url"] !== "" && preg_match("/\Ahttp(s?)\:\/\/.+/", $post_params["img_url"]) === 1 ) {
			$img_file = $this->_saveCoverImagePath($post_params["img_url"]);
			if ($img_file === false){
				Server::send404Header("system error");
				return false;
			}
		}

		// 登録
		$DaoAlbums = new DaoAlbums();
		$add_result = $DaoAlbums->add(
			$post_params["artist"],
			$post_params["title"],
			$post_params["year"] === "" ? null : $post_params["year"],
			$post_params["img_url"],
			$img_file,
			$tracks
		);

		header("Content-Type:application/json; charset=UTF-8");
		echo json_encode($add_result, true);
		return true;
	}

	/**
	 * save cover image path
	 * @param string $img_url
	 * @return string
	 */
	private function _saveCoverImagePath($img_url)
	{
		$src = file_get_contents($img_url);
		if ( $src !== false ) {
			$img_file = sha1($img_url . mt_rand(10000000, 99999999)) . ".jpg";
			$dir = substr($img_file, 0,4);
			$img_path = substr($img_file, 4, strlen($img_file));
			$sep = "/";
			$subdir = implode($sep, preg_split('//', $dir, -1, PREG_SPLIT_NO_EMPTY)) . $sep;
			$dir_path = __DIR__ . "/../../../../../files/covers/{$subdir}";
			// dir がない場合は作成
			if (! file_exists($dir_path) && ! mkdir($dir_path, 0777, true) ) {
				return false;
			}
			if (! file_put_contents($dir_path.$img_path, $src)) {
				return false;;
			}
			return $subdir.$img_path;
		}
		return false;
	}

}
