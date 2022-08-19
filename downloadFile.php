<?php
$filename = urlencode($_REQUEST['file']);
$sname = htmlentities($_REQUEST['sname'],ENT_QUOTES);

header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
@readfile($module->getSafePath($sname, EDOC_PATH));
?>