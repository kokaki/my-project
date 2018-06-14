<?php
/*
 *	API用トラック情報
 */
Class TrackDTO {
	// メンバー変数
	public
		$library_track_id,	// 楽曲ID[LT1]
		$track_set_no,		// 組番号[LT28]
		$track_no,		// Track No.[LT29]
		$track_title,		// 曲名[LT4] title
		$track_title_rdg,	// 曲名ヨミ[LT6] title_rdg
		$track_title_en,	// 曲名英字[LT8] title_eng
		$artist_name,		// アーティスト名[LT15] artist
		$artist_name_rdg,	// アーティスト名ヨミ[LT17] artist_rdg
		$artist_name_en,	// アーティスト名英字[LT19] artist_eng
		$track_label,		// レーベル名[LT22] label
		$track_writer,		// 作詞者[LT24] writer
		$track_composer,	// 作曲者[LT25]
		$track_arranger,	// 編曲者[LT26]
		$track_translator,	// 訳詞者[LT27] translator
		$track_genre,		// ジャンル[LT31]
		$track_broadcast_copyright,	// 著作権情報 放送権 種別
		$track_broadcast_code,		// 著作権情報 放送権 管理コード
		$track_distribution_copyright,	// 著作権情報 配信権 種別
		$track_distribution_code,	// 著作権情報 配信権 管理コード
		$track_jasrac_code,	// JASRACコード
		$track_elicense_code,	// e-Licenseコード
		$track_isrc,		// ISRCコード
		$track_il,		// JASRAC Ｉ／Ｖ区分[LT61]
		$track_min,		// 演奏時間　分
		$track_sec;		// 演奏時間　秒
}
?>
