<?php
require_once('dto/PackageDTO.php');
/*
 *	API用データ情報
 */
class DataDTO {
	// メンバー変数
	public
		$keyword,		// 検索キーワード
		$kind,			// パッケージ種別
		$cabinet_no,	// 棚番号
		$set_total,		// 総組数
		$track_total,	// 総トラック数
		$folder_name,	// 格納フォルダ名
		$PackageInfo;		// パッケージ情報
}
?>