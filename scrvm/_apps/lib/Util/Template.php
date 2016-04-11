<?php
/**
 * Template.php
 * @author mgng
 */

namespace lib\Util;

/**
 * テンプレートクラス
 * @author mgng
 * @package lib\Util
 */
class Template
{
	/**
	 * テンプレートディレクトリ
	 * @var string
	 */
	public $template_dir = "";

	/**
	 * アサイン変数
	 * @var array
	 */
	protected $_assign_data = array();

	/**
	 * コンストラクタ
	 * @return boolean
	 */
	public function __construct()
	{
		return true;
	}

	/**
	 * デストラクタ
	 * @return boolean
	 */
	public function __destruct()
	{
		return true;
	}

	/**
	 * 変数をアサインする。
	 * すでに変数が設定されていた場合、元の配列と引数配列をマージ(array_merge)する。
	 * @param array $data テンプレートにアサインする連想配列 default 空配列
	 * @return \lib\Util\Template
	 */
	public function assign( array $data = array() )
	{
		$this->_assign_data = array_merge($this->_assign_data, $data);
		return $this;
	}

	/**
	 * アサインされている変数配列を返す
	 * @return array
	 */
	public function getAssignData()
	{
		return $this->_assign_data;
	}

	/**
	 * アサインデータ配列をクリアする。
	 * @return \lib\Util\Template
	 */
	public function clearAssignData()
	{
		$this->_assign_data = array();
		return $this;
	}

	/**
	 * テンプレートを出力する。$is_display = false にすると出力しない。
	 * @param string $template テンプレートファイル名
	 * @param boolean $is_display 標準出力する場合はtrue。default true
	 * @return string テンプレートに変数を埋め込んだ文字列
	 */
	public function display( $template, $is_display = true )
	{
		extract( $this->_assign_data );
		ob_start();
		require $this->template_dir . $template;
		$_contents = ob_get_contents();
		ob_end_clean();
		$contents = $this->_includeSsi($_contents);
		if ( $is_display ) {
			echo $contents;
		}
		return $contents;
	}

	/**
	 * $contentsから SSI 記述を正規表現で抜き出し該当SSIファイルを埋め込んで返却する。
	 * @param string $contents 文字列
	 * @return string
	 * @see \lib\Util\Server
	 */
	private function _includeSsi($contents)
	{
//		$pattern = '/<\!\-\-\s*#include\s+virtual\s*=\s*\"(.+?)"\s*\-\->/uis';
//		$matches = array();
//		preg_match_all($pattern, $contents, $matches);
//		if ( count( $matches ) === 0 ) {
//			return $contents;
//		}
//		$doc_root = \lib\Util\Server::env("DOCUMENT_ROOT");
//		for($i=0, $len = count($matches[0]); $i<$len; $i++) {
//			$search = $matches[0][$i];
//			$path = $matches[1][$i];
//			$buf = file_get_contents( $doc_root . $path );
//			$from_enc = mb_detect_encoding($buf, mb_detect_order());
//			$contents = str_replace($search, mb_convert_encoding($buf, "UTF-8", $from_enc), $contents);
//		}
		return $contents;
	}

}

