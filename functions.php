<?php

function getImageToDisplay($edoc){
    $img_logo = '';
    if($edoc != ''){
        $sql = "SELECT stored_name,doc_name,doc_size FROM redcap_edocs_metadata WHERE doc_id=" . $edoc;
        $q = db_query($sql);

        if ($error = db_error()) {
            die($sql . ': ' . $error);
        }

        while ($row = db_fetch_assoc($q)) {
            $img_logo = 'downloadFile.php?sname=' . $row['stored_name'] . '&file=' . $row['doc_name'];
        }
    }

    return $img_logo;
}

function isUserExpiredOrSuspended($username,$field){
    $sql = "SELECT * FROM redcap_user_information WHERE username = '".$username."' AND ".$field." IS NOT NULL";
    $result = db_query($sql);
    if (db_num_rows($result) > 0) {
        return true;
    }
    return false;
}

function printFile($module,$edoc, $type){
    $file = "";
    if($edoc != ""){
        $sql = "SELECT stored_name,doc_name,doc_size,mime_type FROM redcap_edocs_metadata WHERE doc_id=" . $edoc;
        $q = db_query($sql);

        if ($error = db_error()) {
            die($sql . ': ' . $error);
        }

        while ($row = db_fetch_assoc($q)) {
            $url = 'downloadFile.php?sname=' . $row['stored_name'] . '&file=' . urlencode($row['doc_name']);
            $base64 = base64_encode(file_get_contents(EDOC_PATH.$row['stored_name']));
            if($type == "img"){
                $file = '<br/><div class="inside-panel-content"><img src="' . $module->getUrl($url,true) . '" style="display: block; margin: 0 auto;"></div>';
            }else if($type == "logo"){
                $file = '<img src="data:'.$row['mime_type'].'base64,' . $base64. '" style="padding-bottom: 30px;width: 450px;">';
            }else if($type == "imgpdf"){
                $file = '<div style="max-width: 450px;height: 500px;"><img src="' . $module->getUrl($url,true) . '" style="display: block; margin: 0 auto;width:450px;height: 450px;"></div>';
            }else{
                $file = '<br/><div class="inside-panel-content"><a href="'.$module->getUrl($url,true).'" target="_blank"><span class="fa fa-file-o"></span> ' . $row['doc_name'] . '</a></div>';
            }

        }
    }
    return $file;
}
?>