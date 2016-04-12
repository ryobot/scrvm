<?php
/**
 * File.php
 * @author mgng
 */

namespace lib\Util;

/**
 * ファイル操作系クラス
 * @author mgng
 * @package lib\Util
 */
class File
{

	/**
	 * ファイルアップロードエラー時のエラー文言を返す。
	 * @param integer $code エラーコード
	 * @return string エラー文言
	 */
	public static function getUploadErrorMessage($code)
	{
		if ( $code === UPLOAD_ERR_OK ) {
			return "";
		}
		if ( $code === UPLOAD_ERR_INI_SIZE ) {
			return "ファイルは、システムで対応できるファイルサイズを超えています。";
		}
		if ( $code === UPLOAD_ERR_FORM_SIZE ) {
			return "ファイルは、フォームで指定された最大ファイルサイズを超えています。";
		}
		if ( $code === UPLOAD_ERR_PARTIAL ) {
			return "ファイルは一部のみしかアップロードされていません。";
		}
//		if ( $code === UPLOAD_ERR_NO_FILE ) {
//			return "ファイルはアップロードされませんでした。";
//		}
		if ( $code === UPLOAD_ERR_NO_TMP_DIR ) {
			return "テンポラリフォルダがありません。";
		}
		if ( $code === UPLOAD_ERR_CANT_WRITE ) {
			return "ディスクへの書き込みに失敗しました。";
		}
		if ( $code === UPLOAD_ERR_EXTENSION ) {
			return "拡張モジュールがファイルのアップロードを中止しました";
		}
		return "未知のエラーが発生しました。";
	}

}
