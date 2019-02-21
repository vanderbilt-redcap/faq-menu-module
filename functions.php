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
?>