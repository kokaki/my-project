<?php
require_once('dto/TrackDTO.php');
/*
 *	API用パッケージ情報
 */
class PackageDTO {
	// メンバー変数
	public
		$ID,			// パッケージ識別ＩＤ
		$library_package_id,	// パッケージID[LP1]
		$import_type,			// パッケージ種別[LP43]
		$package_title,			// タイトル[LP3]/title
		$package_title_rdg,		// タイトルヨミ[LP5] title_rdg
		$package_title_en,		// タイトル英字[LP7] title_eng
		$artist_name,			// アーティスト名[LP14] artist
		$artist_name_rdg,		// アーティスト名ヨミ[LP16] artist_rdg
		$artist_name_en,		// アーティスト名英字[LP18] artist_eng
		$set_total,				// 組数[LP21]
		$package_cd_code,				// CD番号[LP23]
		$track_count,			// 収録曲数[LP24]
		$cabinet_no,			// 棚番号[LP27]
		$cabinet_no_disp,		// 棚番号（表示用）[LP28]
		$package_label,			// レーベル名[LP29] label
		$package_sales_date,	// 発売日[LP32] release_date
		$package_package_type,	// 商品形態[LP36] package_type
		$package_media_type,	// 商品形態（詳細） 1:CDアルバム固定
		$package_genre,			// ジャンル[LP41]
		$package_jan_code,		// JANコード[LP45] jan_code
		$tracks;
}
?>
