<?php
/**
 * /lib/Scrv/Action/Spotify/Search.php
 * @author mgng
 */

namespace lib\Scrv\Action\Spotify;
use lib\Scrv\Action\Base as Base;
use lib\Util\Spotify as UtilSpotify;
use lib\Util\Server as Server;

/**
 * Spotify Search class
 * @author mgng
 */
class Search extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		$q = mb_trim(Server::get("q", ""));
		if ( $q === "" ) {
			Server::send404Header("404 not found");
			return false;
		}

		// インスタンス生成
		$S = new UtilSpotify();
		$S->setConfig(array(
			"client_id" => self::$_common_ini["spotify"]["client_id"],
			"client_secret" => self::$_common_ini["spotify"]["client_secret"],
		));

		header("Content-Type:application/json; charset=utf-8");

		// config cache確認
		$spotify_config_cache = __DIR__ . "/../../../../data/cache/spotify/cache.json";
		$config_src = is_readable($spotify_config_cache) ? file_get_contents($spotify_config_cache) : "null";
		$config_json = json_decode($config_src);
		$access_token = null;
		if ($config_json === null || time() > $config_json->expires_timestamp){
			$result = $S->getAccessToken();
			if ($result["error"] !== "") {
				echo "[]";
				return false;
			}
			$response_json = json_decode($result["response"]);
			if ($response_json === null || !isset($response_json->access_token)){
				echo "[]";
				return false;
			}
			// アクセストークン、expires_in から有効期限の時間を算出(安全のため60秒少なくしておく)してキャッシュ作成
			$access_token = $response_json->access_token;
			$expires_in = $response_json->expires_in;
			$expires_timestamp = date(time() + $expires_in - 60);
			file_put_contents($spotify_config_cache, json_encode(array(
				"access_token" => $access_token,
				"expires_timestamp" => $expires_timestamp,
				"expires_date" => date("Y-m-d H:i:s", $expires_timestamp),
			)), LOCK_EX);
		} else {
			$access_token = $config_json->access_token;
		}

		// 検索
		$type = "album";
		$search_result = $S->search($access_token, $q, $type);
		$json = json_decode($search_result["response"]);

		$album_data = array();
		foreach($json->albums->items as $data) {
			$artists = array();
			foreach($data->artists as $artist){
				$artists[] = $artist->name;
			}
			$album_data[] = array(
				"artist" => implode(",", $artists),
				"title" => $data->name,
				"url" => $data->external_urls->spotify,
			);
		}

		echo json_encode($album_data);

		return true;
	}

}
