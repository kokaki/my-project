<?php
/*
 *	API用データ情報
 */
class ResultDTO {
	// メンバー変数
	public
		$status,		// ステータス
		$company_code,	// 会社コード
		$keyword,		// 検索キーワード
		$hash_code,		// 接続用HASHコード
		$massages;		// メッセージ
}
class MessageDTO {
	// メンバー変数
	public
		$code,
		$message;
}
?>