<?php
/**
 * Images.php
 * @author mgng
 */

namespace lib\Util;

/**
 * Images class
 * @author mgng
 * @package lib\Util
 */
class Images
{
	/**
	 * サムネイル生成
	 * @param string $src 対象画像ファイルパス
	 * @param string $dest 保存先ファイルパス
	 * @param int $width 伸張する幅
	 * @param int $height 伸張する高さ
	 * @param bool $auto 長辺に合わせて自動リサイズするかどうか
	 * @return boolean
	 */
	public function makeThumbnail($src, $dest, $width, $height, $auto=false)
	{
		$imagesize = getimagesize($src);
		if ( $imagesize === false ) {
			return false;
		}
		$src_width = $imagesize[0];
		$src_height = $imagesize[1];
		$src_mime = $imagesize["mime"];
		$src_ext = str_replace("image/", "", $src_mime);

		// src gd読み込み
		$src_gd = call_user_func( "imagecreatefrom{$src_ext}", $src );
		if ( $src_gd === false ) {
			return false;
		}

		// リサイズ
		if ($auto) {
			$auto_resize = $this->getAutoSize($src_width, $src_height, $width, $height);
			$width = $auto_resize["width"];
			$height = $auto_resize["height"];
		}
		$dest_gd = imagecreatetruecolor($width, $height);
		imagealphablending($dest_gd, false);	// 透過対応
		imagesavealpha($dest_gd, true);	// 透過対応
		imagecopyresampled($dest_gd, $src_gd, 0,0,0,0, $width, $height, $src_width, $src_height);

		// 出力
		return call_user_func("image{$src_ext}", $dest_gd, $dest);
	}

	/**
	 * 縦横幅を自動調整したwdth,heightを返す
	 * @param integer $src_w 元ファイル幅
	 * @param integer $src_h 元ファイル高さ
	 * @param integer $resize_w リサイズ用幅
	 * @param integer $resize_h リサイズ用高さ
	 * @return array
	 */
	public function getAutoSize($src_w, $src_h, $resize_w, $resize_h)
	{
		$per = ( $resize_w <= $resize_h ) ? ( $resize_w / $src_w ) : ( $resize_h / $src_h );
		return array(
			"width" => ceil( $src_w * $per ),
			"height" => ceil( $src_h * $per ),
		);
	}

	/**
	 * 画像形式チェック。許可しない画像の場合はfalse, OKの場合は getimagesize の結果配列を返す
	 * @param string $path img file path
	 * @param array $allow_types 許可する画像形式 default["jpeg"]
	 * @return boolean|array
	 */
	public function checkType($path, array $allow_types = array("jpeg"))
	{
		$imagesize = getimagesize($path);
		if ( $imagesize === false ) {
			return false;
		}
		$reg = implode("|", $allow_types);
		if ( ! isset($imagesize["mime"])
			|| preg_match("/\Aimage\/({$reg})\z/", $imagesize["mime"]) !== 1
		) {
			return false;
		}
		return $imagesize;
	}

}
