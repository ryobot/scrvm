<?php
/**
 * Images.php
 * @author mgng
 */

namespace lib\Util;

/**
 * メール送信クラス
 * @author mgng
 * @package lib\Util
 */
class Images
{
	/**
	 * サムネイル生成
	 * @param type $src 対象画像ファイルパス
	 * @param type $dest 保存先ファイルパス
	 * @param type $width 伸張する幅
	 * @param type $height 伸張する高さ
	 * @return boolean
	 */
	public function makeThumbnail($src, $dest, $width, $height)
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
		$dest_gd = imagecreatetruecolor($width, $height);
		imagealphablending($dest_gd, false);	// 透過対応
		imagesavealpha($dest_gd, true);	// 透過対応
		imagecopyresampled($dest_gd, $src_gd, 0,0,0,0, $width, $height, $src_width, $src_height);

		// 出力
		return call_user_func("image{$src_ext}", $dest_gd, $dest);
	}

}
