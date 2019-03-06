<?php

if(array_key_exists('configure',$_REQUEST)){

global $Proj,$lang;

$dictionary_array = array (
    'A' => array ( 2 => 'record_id', 3 => 'help_show_y', 4 => 'help_category', 5 => 'help_question', 6 => 'help_answer',7 => 'help_document',8 => 'help_document2', 9 => 'help_image', 10 => 'help_videoformat', 11 => 'help_videolink', 12 => 'help_embedcode' ) ,
    'B' => array ( 2 => 'faq_item', 3 => 'faq_item', 4 => 'faq_item', 5 => 'faq_item', 6 => 'faq_item', 7 => 'faq_item', 8 => 'faq_item', 9 => 'faq_item', 10 => 'faq_item', 11 => 'faq_item', 12 => 'faq_item' ) ,
    'C' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 => 'Supporting Materials (File, Image, Video)',8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'' ) ,
    'D' => array ( 2 => 'text', 3 => 'yesno', 4 => 'dropdown', 5 => 'notes', 6 => 'notes' ,7 => 'file', 8 => 'file', 9 => 'file', 10 => 'radio', 11 =>'text', 12 =>'notes' ) ,
    'E' => array ( 2 => 'Record ID', 3 =>"<div style=\"color:black; text-align:center; border: 1px #d35400; background-color: #e67e22; padding:6px; max-width:800px;\">Show this FAQ Item?</div>",
                    4 => 'Question Category<div style="font-weight:lighter;font-style:italic">Edit this variable in the Online Designer to change the category options. Categories will display top to bottom on the FAQ page.</div>',
                    5 => 'Question',
                    6 => 'Answer <div style="font-weight:lighter;font-style:italic">Text can include basic HTML, including text formatting, links, and lists.</div>',
                    7 => 'Supporting Document #1<div style="font-weight:lighter;font-style:italic">Optional document. Download link will be displayed below the answer text.</div>',
                    8 => 'Supporting Document #2<div style="font-weight:lighter;font-style:italic">Optional document. Download link will be displayed below the answer text.</div>',
                    9 => 'Supporting Image <div style="font-weight:lighter;font-style:italic">Optional image to be displayed below the answer text.</div>',
                    10 => 'Choose Video Format <div style="font-weight:lighter;font-style:italic">Optional video to be displayed below the answer text.</div>',
                    11 => 'Supporting Video Link <div style="font-weight:lighter;font-style:italic">Link to hosted video. The FAQ Builder will add the necessary iframe code.</div>',
                    12 => 'Video Embed Code <div style="font-weight:lighter;font-style:italic">HTML iframe code generated by website (e.g., Youtube)</div>' ) ,
    'F' => array ( 2 =>'', 3 =>'', 4 => "3, Protocol | 1, Study Clarifications | 2, Data Quality | 4, Invoicing | 5, Accounts and Login | 99, Harmonist Project", 5 =>'', 6 =>'', 7 => '',8 => '', 9 =>'', 10 =>"1, Video URL | 2, Video embed code / HTML (e.g., Youtube)",11 =>'', 12 =>'' ),
    'G' => array ( 2 => '',3 =>'', 4 =>'', 5 => '',6 =>'', 7 => '',8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'' ) ,
    'H' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'' ) ,
    'I' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'' ) ,
    'J' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'' ) ,
    'K' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'' ) ,
    'L' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 => '', 10 => '', 11 =>"[help_videoformat] = '1'", 12 =>"[help_videoformat] = '2' ") ,
    'M' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 => '', 10 => '', 11 =>'', 12 =>'') ,
    'N' => array ( 2 =>'', 3 => 'RH', 4 => 'RH', 5 => 'LV', 6 => 'LV', 7 => 'RH' ,8 => 'RH',9 => 'RH', 10 => '', 11 =>'RH', 12 =>'RH') ,
    'O' => array ( 2 =>'', 3 =>'', 4 => '',5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 =>'', 10 => '', 11 =>'', 12 =>'') ,
    'P' => array ( 2 =>'', 3 =>'', 4 => '',5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'') ,
    'Q' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'') ,
    'R' => array ( 2 =>'', 3 =>'', 4 =>'', 5 =>'', 6 =>'', 7 =>'', 8 =>'', 9 =>'', 10 =>'', 11 =>'', 12 =>'' )
    );

// Return warnings and errors from file (and fix any correctable errors)
list ($errors_array, $warnings_array, $dictionary_array) = \MetaData::error_checking($dictionary_array);

// Set up all actions as a transaction to ensure everything is done here
db_query("SET AUTOCOMMIT=0");
db_query("BEGIN");

// Save data dictionary in metadata table
$sql_errors = \MetaData::save_metadata($dictionary_array);

// Display any failed queries to Super Users, but only give minimal info of error to regular users
if (count($sql_errors) > 0) {

    // ERRORS OCCURRED, so undo any changes made
    db_query("ROLLBACK");
    // Set back to previous value
    db_query("SET AUTOCOMMIT=1");

    print  "<div class='red'>
					<b>{$lang['global_01']}:</b><br>
					{$lang['design_158']}";
    // Display failed queries only to super users for troubleshooting
    if (SUPER_USER)
    {
        print  "<br><br>{$lang['design_159']}<br>";

        foreach ($sql_errors as $this_query)
        {
            print "<p>".htmlspecialchars($this_query, ENT_QUOTES).";</p>";
        }
    }
    print  "</div>";


}
else
{
    // SURVEY QUESTION NUMBERING (DEV ONLY): Detect if any forms are a survey, and if so, if has any branching logic.
    // If so, disable question auto numbering.
    if ($status < 1)
    {
        foreach (array_keys($Proj->surveys) as $this_survey_id)
        {
            $this_form = $Proj->surveys[$this_survey_id]['form_name'];
            if ($Proj->surveys[$this_survey_id]['question_auto_numbering'] && \Design::checkSurveyBranchingExists($this_form))
            {
                // Survey is using auto question numbering and has branching, so set to custom numbering
                $sql = "update redcap_surveys set question_auto_numbering = 0 where survey_id = $this_survey_id";
                db_query($sql);
            }
        }
    }

    // COMMIT CHANGES
    db_query("COMMIT");
    // Set back to previous value
    db_query("SET AUTOCOMMIT=1");

    // SUCCESS - reload page so that menu form names will update (for development projects)

//    print  "<br><br><img src='" . APP_PATH_IMAGES . "progress_circle.gif'> &nbsp;<b>{$lang['design_160']}</b><br>";
//    print  "<script type='text/javascript'>
//					window.location.href = '".$module->getUrl('configure.php?upload_success')."';
//				</script>";

    echo json_encode(array(
        'status' => 'success',
        'message' => 'Data Dictionary installed'
    ));

}
}else{
    print  "<script type='text/javascript'>
					
				</script>";
}
?>