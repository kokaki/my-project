<?php
/*
 *  指定されたパラメータをもとに、接続可否確認を行い、音源メタ情報を送信
 *  POSTパラメータ:
 *      company_code    : 会社コード
 *      keyword         : 検索キーワード
 *      hash_code       : 接続用HASHコード
 *  戻り値:
 *      JSON形式
 *  URL例:
 *      http://localhost/api/meta_search.php?company_code=20000198&keyword=ESCB-1591
 *                      &hash_code=638c96d2ac0944cce89027dd6865d669e616692f79b384c4c179211469b79701
 *  api/
 *   +- rip/
 *    +- meta_search.php
 *    +- dao/
 *     +- DAO...
 *    +- dto/
 *     +- DTO...
 *   +- common.conf.php
 */

//require_once('MDB2.php');不要
require_once($_SERVER['DOCUMENT_ROOT']. '/api/common.conf.php');

//require_once('json/encode_decode.php');不要

require_once('dao/PackageDAO.php');
require_once('dao/TrackDAO.php');
require_once('dto/DataDTO.php');
require_once('dto/ResultDTO.php');

define('_COMPANY', 20000198);           // 会社コード(チェック用)
define('_HASH', '638c96d2ac0944cce89027dd6865d669e616692f79b384c4c179211469b79701');
                                        // 接続用HASHコード(チェック用)
define('_STATUS_OK', 'OK'); // OK
define('_STATUS_NG', 'NG'); // NG

define('_IMPORT_TYPE_CABINET', 'C');    // 棚情報
define('_IMPORT_TYPE_JMD', 'M');        // JMD(eCATS)情報
define('_IMPORT_TYPE_ELICENSE', 'S');   // ライブラリミュージック情報
define('_IMPORT_TYPE_NONE', 'X');       // メタ情報なし

define('_RETURN_CODE', "\r\n");       	// 改行コード:「'」シングルクォートではなく、「"」ダブルクォートでくくらないとダメでした.

define('_KAKKO_LEFT', '(');             // 左括弧
define('_KAKKO_RIGHT', ')');            // 右括弧

//----------------------------------------------------------------------------
// POSTパラメータ受信およびチェック
$error_message = null;
$result = new ResultDTO();
$result->status = _STATUS_OK;

// 会社コード
$_company_code = null;
if(!empty($_POST['company_code'])){
    $_company_code = $_POST['company_code'];
    $result->company_code = $_company_code;
} else {
    $result->status = _STATUS_NG;
    $message = new MessageDTO();
    $message->code = 1001;
    $message->message = '会社コードが指定されていません.';
    $result->messages[] = $message;
}
// 検索キーワード
$_keyword = null;
if(!empty($_POST['keyword'])){
    $_keyword = $_POST['keyword'];
    $result->keyword = $_keyword;
} else {
    $result->status = _STATUS_NG;
    $message = new MessageDTO();
    $message->code = 1002;
    $message->message = '検索キーワードが指定されていません.';
    $result->messages[] = $message;
}
// 接続用HASHコード
$_hash_code = null;
if(!empty($_POST['hash_code'])){
    $_hash_code = $_POST['hash_code'];
    $result->hash_code = $_hash_code;
} else {
    $result->status = _STATUS_NG;
    $message = new MessageDTO();
    $message->code = 1003;
    $message->message = '接続用HASHコードが指定されていません.';
    $result->messages[] = $message;
}

// POSTパラメータエラー時
if ($result->status != _STATUS_OK) {
    $result_array['result'] = $result;
    echo json_encode($result_array);
    return;
}

//----------------------------------------------------------------------------
// 会社コードおよび接続用HASHコードチェック
if (($_company_code != _COMPANY) || ($_hash_code != _HASH)) {
    $result->status = _STATUS_NG;
    $message = new MessageDTO();
    $message->code = 2001;
    $message->message = '会社コードもしくは接続用HASHコードが間違っています.';
    $result->messages[] = $message;
    $result_array['result'] = $result;
    echo json_encode($result_array);
    return;
}

//----------------------------------------------------------------------------
// DB接続
//$db = MDB2::factory($dsn); MDB2からPDOに
//if (PEAR::isError($db)) {
//    die('[MDB2::factory]' . $db->getMessage());
//}
//$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
try {
    $db = new PDO($dsn);
} catch (PDOException $e) {
    print('Connection failed:'.$e->getMessage()); die();
}
//----------------------------------------------------------------------------
// 棚情報取得
$table_name = 'library_package_' . $_company_code;
$import_type = '"' . _IMPORT_TYPE_CABINET . '"';
$package_dao = new PackageDAO();
$package_c = $package_dao->getPackageBySearchKey($db, $table_name, $import_type, $_keyword);

//----------------------------------------------------------------------------
// JMD(eCATS)情報取得
$table_name = 'library_package_jmd';
$import_type = '"' . _IMPORT_TYPE_JMD . '"';
$package_dao = new PackageDAO();
$package_m = $package_dao->getPackageBySearchKey($db, $table_name, $import_type, $_keyword);

//----------------------------------------------------------------------------
// ライブラリミュージック情報取得
$table_name = 'library_package_elicense';
$import_type = '"' . _IMPORT_TYPE_ELICENSE . '"';
$package_dao = new PackageDAO();
$package_s = $package_dao->getPackageBySearchKey($db, $table_name, $import_type, $_keyword);

//----------------------------------------------------------------------------
// 有効なメタ情報の判断
$flag = _IMPORT_TYPE_NONE;
// 棚情報がある場合
if (!is_null($package_c)) {
    // JMD(eCATS)情報がある場合
    if (!is_null($package_m)) {
        // 組数,収録曲数の両方が同じ場合
        if (($package_c->set_total == $package_m->set_total)
        && ($package_c->track_count == $package_m->track_count)) {
            $flag = _IMPORT_TYPE_CABINET;       // 棚情報を使用
        } else {
            $flag = _IMPORT_TYPE_JMD;           // JMD(eCATS)情報を使用
        }
    } else {
        // JMD(eCATS)情報がない場合
        // ライブラリミュージック情報がある場合
        if (!is_null($package_s)) {
            // 組数,収録曲数の両方が同じ場合
            if (($package_c->set_total == $package_s->set_total)
            && ($package_c->track_count == $package_s->track_count)) {
                $flag = _IMPORT_TYPE_CABINET;   // 棚情報を使用
            } else {
                $flag = _IMPORT_TYPE_ELICENSE;  // ライブラリミュージック情報を使用
            }
        } else {
            // ライブラリミュージック情報がない場合
            $flag = _IMPORT_TYPE_CABINET;       // 棚情報を使用
        }
    }
} else {
    // 棚情報がない場合
    // JMD(eCATS)情報がある場合
    if (!is_null($package_m)) {
        $flag = _IMPORT_TYPE_JMD;               // JMD(eCATS)情報を使用
    } else {
        // ライブラリミュージック情報がある場合
        if (!is_null($package_s)) {
            $flag = _IMPORT_TYPE_ELICENSE;      // ライブラリミュージック情報を使用
        } else {
            $flag = _IMPORT_TYPE_NONE;          // メタ情報なし
        }
    }
}

//----------------------------------------------------------------------------
// 有効なパッケージ情報をセット
$json_data = new DataDTO();
switch ($flag) {
    case _IMPORT_TYPE_CABINET:      // 棚情報
        $table_name = 'library_track_' . $_company_code;
        $json_data->PackageInfo = $package_c;
        break;
    case _IMPORT_TYPE_JMD:          // JMD(eCATS)情報
        $table_name = 'library_track_jmd';
        $json_data->PackageInfo = $package_m;
        break;
    case _IMPORT_TYPE_ELICENSE:     // ライブラリミュージック情報
        $table_name = 'library_track_elicense';
        $json_data->PackageInfo = $package_s;
        break;
    case _IMPORT_TYPE_NONE:         // メタ情報なし
    default:
        // COMMENT:該当なしの場合でもリッピングするときは、
        // 検索キーワードを適切な項目にセット、メタデータを
        // 生成することになる.
        $result->status = _STATUS_NG;
        $message = new MessageDTO();
        $message->code = 3001;
        $message->message = '検索キーワードに該当する楽曲情報が見つかりません.';
        $result->messages[] = $message;
        $result_array['result'] = $result;
        echo json_encode($result_array);
        return;
}

// トラック情報取得
$track_dao = new TrackDAO();
$json_data->PackageInfo->tracks = $track_dao->getTrackById($db, $table_name, $json_data->PackageInfo->library_package_id);

// BODY部編集(JSON形式)
// パッケージ情報
$json_body = "";
$json_body .= 'cabinet_no, ' . $json_data->PackageInfo->cabinet_no . _RETURN_CODE;
$json_body .= 'jan_code, ' . $json_data->PackageInfo->package_jan_code . _RETURN_CODE;
$json_body .= 'record_no, ' . $json_data->PackageInfo->package_cd_code . _RETURN_CODE;
$json_body .= 'title, ' . $json_data->PackageInfo->package_title . _KAKKO_LEFT . $json_data->Package->artist_name . _KAKKO_RIGHT . _RETURN_CODE;

// トラック情報
$set_total = 0; // 組番号の最大数
foreach ($json_data->PackageInfo->tracks as $track) {
    $json_body .= 'set_no, ' . $track->set_no . _RETURN_CODE;
    $json_body .= 'track_no, ' . $track->track_no . _RETURN_CODE;
    $json_body .= 'title, ' . $track->track_title . _KAKKO_LEFT . $track->artist_name . _KAKKO_RIGHT . _RETURN_CODE;
    // 組番号の最大数を求める
    if ($set_total < $track->set_no) {
        $set_total = $track->set_no;
    }
}

// DATA部(package以外)編集
$json_data->keyword = $_keyword;                                // キーワード
$json_data->kind = $flag;                                       // パッケージ種別
$json_data->cabinet_no = $json_data->PackageInfo->cabinet_no;   // 棚番号
$json_data->set_total = $set_total;                             // 総組数
$json_data->track_total = sizeof($json_data->PackageInfo->tracks);   // 総トラック数(tracksのサイズから導出)
$json_data->folder_name = getFolderName($json_data);            // 格納フォルダ

// BODY部とDATA部を結合し出力
header('Content-Type: application/json; charset=utf-8');
$result_json['result'] = $result;
$result_json['body'] = $json_body;
$result_json['data'] = $json_data;
echo json_encode($result_json);

return;

/*
 *  格納フォルダ名を生成
 *  棚番号_CD番号_JANコード TODO:ちゃんと考える
 */
function getFolderName($p_data) {
    $separator = '_';

    $result = '';
    $result .= $p_data->PackageInfo->cabinet_no;        // 棚番号
    $result .= $separator;
    $result .= $p_data->PackageInfo->package_cd_code;         // CD番号
    $result .= $separator;
    $result .= $p_data->PackageInfo->package_jan_code;  // JANコード

    // フォルダ名に適切ではない文字を除去 TODO:除去対象文字の精査
    $vowels = array('/', '"', '\\', '~', ',', '[', ']', '{', '}', '(', ')');

    return str_replace($vowels, "", $result);
}
?>
