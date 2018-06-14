<?php
require_once('dto/PackageDTO.php');
/*
 *	パッケージ情報取得DAO
 */
class PackageDAO {
	/*
	 *	指定されたキーで、棚番号、CD番号、JANコードを検索
	 *	(3つの項目のいづれかに該当、もしくは、該当なし)
	 *	(重複データはない。棚番号とCD番号が同じデータとか)
	 * $p_db			: データベース接続情報
	 * $p_table_name	: テーブル名
	 * $p_import_type	: パッケージ種別
	 * $p_search_key	: 検索キーワード
	 */
	function getPackageBySearchKey($p_db, $p_table_name, $p_import_type, $p_search_key) {
		$sql_select_1 = ' SELECT library_package_id, ';
		$sql_select_2 = ' AS import_type, title, title_rdg, title_eng, artist, artist_rdg, artist_eng, ';
		$sql_select_3 = ' set_total, record_no, track_count, cabinet_no, cabinet_no_disp, label, release_date, package_type, genre, jan_code ';
		$sql_from = ' FROM ';
		$sql_where = ' WHERE record_no = ? OR cabinet_no = ? OR jan_code = ? ';
		$sql_orderBy = ' ORDER BY library_package_id;';
		$sql = $sql_select_1 . $p_import_type . $sql_select_2 . $sql_select_3 . $sql_from . $p_table_name .
				$sql_where . $sql_orderBy;
		$types = array('text', 'text', 'text');
		try {
		    $stmt = $p_db->prepare($sql, $types);
            $res = $stmt->execute(array($p_search_key, $p_search_key, $p_search_key));
    		$package = null;
    		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        		$package = new PackageDTO();
    			$package->library_package_id = $row['library_package_id'];
    			$package->import_type = $row['import_type'];
    			$package->package_title = $row['title'];
    			$package->package_title_rdg = $row['title_rdg'];
    			$package->package_title_en = $row['title_eng'];
    			$package->artist_name = $row['artist'];
    			$package->artist_name_rdg = $row['artist_rdg'];
    			$package->artist_name_en = $row['artist_eng'];
    			$package->set_total = $row['set_total'];
    			$package->package_cd_code = $row['record_no'];
    			$package->track_count = $row['track_count'];
    			$package->cabinet_no = $row['cabinet_no'];
    			if( strlen($row['cabinet_no_disp']) > 0 ){	$package->ID = $row['cabinet_no_disp'];
    			}elseif ( strlen($row['jan_code']) > 0 ){	$package->ID = $row['jan_code'];
    			}else{						$package->ID = $row['record_no'];
    			}
    			$package->cabinet_no_disp = $row['cabinet_no_disp'];
    			$package->package_label = $row['label'];
    			$package->package_sales_date = $row['release_date'];
    			$package->package_package_type = $row['package_type'];
    			$package->package_media_type = '1';
    			$package->package_genre = $row['genre'];
    			$package->package_jan_code = $row['jan_code'];
    		}
		} catch (PDOException $e){
		    print('Package $stmt->execute' . $e->getMessage());
		    die();
		}
		return $package;
	}
}
?>
