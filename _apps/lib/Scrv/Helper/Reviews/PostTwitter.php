<?php
/**
 * /lib/Scrv/Helper/Reviews/PostTwitter.php
 * @author mgng
 */

namespace lib\Scrv\Helper\Reviews;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;

// XXX...
require_once __DIR__ . "/../../../Vender/tmhOAuth/tmhOAuth.php";

/**
 * PostTwitter
 * @author mgng
 */
class PostTwitter extends Base
{
	/**
	 * sendTwitter
	 * @param string $album_id
	 * @param string $artist
	 * @param string $title
	 * @param string $review_id
	 * @param string $body
	 * @return resuktSet
	 */
	public function run($album_id, $artist, $title, $review_id, $body)
	{
		$result = getResultSet();
		$tmhOAuth = new \tmhOAuth( array(
			'consumer_key'    => self::$_common_ini["twitter"]['consumer_key'],
			'consumer_secret' => self::$_common_ini["twitter"]['consumer_secret'],
			'user_token'      => $this->_login_user_data["twitter_user_token"],
			'user_secret'     => $this->_login_user_data["twitter_user_secret"],
		) );

		$max_length = 140;
		$content = "{$title}/{$artist}\n{$body}";
		$hashtag = "#scrv";
		$perma_link = Server::getFullHostUrl() . $this->_BasePath . "r/{$review_id}";

		$status = "{$content}\n{$hashtag}\n{$perma_link}";
		$status_length = mb_strlen($status);
		if ( $status_length > $max_length ) {
			$sub_length = $max_length - $status_length;
			$content = mb_substr($content, 0, $sub_length - 3 ); // ちょっと余裕を持たせて
			$status = "{$content}…\n{$hashtag}\n{$perma_link}";
		}

		$code = $tmhOAuth->request('POST',"https://api.twitter.com/1.1/statuses/update.json",array(
			"include_entities" => "true",
			"status" => $status,
		));
		$res = $tmhOAuth->response['response'];
		$result["status"] = $code === 200;
		$result["data"] = array(
			"code" => $code,
			"response" => $res,
		);
		return $result;
	}

}
