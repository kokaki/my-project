<?php
require_once('dto/TrackDTO.php');
/*
 *	トラック情報取得DAO
 */
class TrackDAO {
	/*
	 *	指定されたパッケージIDで、トラック情報を検索。
	 *	(組番号, Track No.で昇順並び替え)
	 * $p_db					: データベース接続情報
	 * $p_table_name			: テーブル名
	 * $p_library_package_id	: パッケージID
	 */
	function getTrackById($p_db, $p_table_name, $p_library_package_id) {
		$sql_select_1 = ' SELECT library_track_id, set_no, track_no, title, title_rdg, title_eng, ';
		$sql_select_2 = ' artist, artist_rdg, artist_eng, label, writer, composer, arranger, translator, ';
		$sql_select_3 = ' genre, jasrac, jasrac_haishin, jasrac_code, jasrac_ivt, isrc, ';
		$sql_select_4 = ' elicense, elicense_other, elicense_code, jrc_hoso, jrc, jrc_code, track_time ';
		$sql_from = ' FROM ';
		$sql_where = ' WHERE library_package_id = ? ';
		$sql_orderBy = ' ORDER BY set_no, track_no;';
		$sql = $sql_select_1 . $sql_select_2 . $sql_select_3 . $sql_select_4 . $sql_from . $p_table_name .
				$sql_where . $sql_orderBy;
//		$types = array('text'); MDB2からPDOに
//		$stmt = $p_db->prepare($sql, $types);
//		$res = $stmt->execute(array($p_library_package_id));
//		if (PEAR::isError($res)) {
//			die('Track $stmt->execute' . $res->getMessage());
//		}
		try {
    		$result = null;
//    		while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) { MDB2からPDOに
    		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        		$track = new TrackDTO();
    			$track->library_track_id = $row['library_track_id'];
    			$track->track_set_no = $row['set_no'];
    			$track->track_no = $row['track_no'];
    			$track->track_title = $row['title'];
    			$track->track_title_rdg = $row['title_rdg'];
    			$track->track_title_en = $row['title_eng'];
    			$track->artist_name = $row['artist'];
    			$track->artist_name_rdg = $row['artist_rdg'];
    			$track->artist_name_en = $row['artist_eng'];
    			$track->track_label = $row['label'];
    			$track->track_writer = $row['writer'];
    			$track->track_composer = $row['composer'];
    			$track->track_arranger = $row['arranger'];
    			$track->track_translator = $row['translator'];
    			$track->track_genre = $row['genre'];
    			if( $row['jasrac'] > 0 ){
    				$track->track_broadcast_copyright = 'JASRAC';
    				$track->track_jasrac = 'true';
    				$track->track_broadcast_code = $row['jasrac_code'];
    			}elseif( $row['elicense'] > 0 ){
    				$track->track_broadcast_copyright = 'eLicense';
    				$track->track_elicense = 'true';
    				$track->track_broadcast_code = $row['elicense_code'];
    			}
    			if( $row['jasrac_haishin'] > 0 ){
    				$track->track_distribution_copyright = 'JASRAC';
    				$track->track_distribution_code = $row['jasrac_code'];
    			}elseif( $row['elicense_other'] > 0 ){
    				$track->track_distribution_copyright = 'eLicense';
    				$track->track_distribution_code = $row['elicense_code'];
    			}
    			$track->track_jasrac_code = $row['jasrac_code'];
    			$track->track_elicense_code = $row['elicense_code'];
    			$track->track_il = $row['jasrac_ivt'];
    			$track->track_isrc = $row['isrc'];
    			// 演奏時間 HHMMSSmmm 000717000 --> 7 min 17 sec
    			$HH = intval(substr($row['track_time'], 0, 2));
    			$track->track_min = strval(intval(substr($row['track_time'], 2, 2)) + 60*$HH);
    			$track->track_sec = strval(intval(substr($row['track_time'], 4, 2)));

    			$result[] = $track;
    		}
		} catch (PDOException $e){
		    print('Track $stmt->execute' . $e->getMessage());
		    die();
		}
		return $result;
	}
}
?>
