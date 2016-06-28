<?php
/**
 * /lib/Scrv/Action/About/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\About;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;

/**
 * About Index class
 * @author mgng
 */
class Index extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		$contents = $this->_Template->assign(array())->display("About/Index.tpl.php", false);

		// Etag用ハッシュ取得
		$etag = $this->_Template->getEtag($contents);
		// キャッシュヘッダとETagヘッダ出力
		header("Cache-Control: max-age=60");
		header("ETag: {$etag}");
		// etagが同じなら304
		$client_etag = Server::env("HTTP_IF_NONE_MATCH");
		if ( $etag === $client_etag ) {
			header( 'HTTP', true, 304 );
			return true;
		}

		echo $contents;
		return true;
	}

}
