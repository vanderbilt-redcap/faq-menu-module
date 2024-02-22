<?php
namespace Vanderbilt\FaqMenuExternalModule;
include_once(__DIR__ . "/functions.php");
require __DIR__ .'/vendor/autoload.php';

$faq_logo = $module->getProjectSetting('faq-logo');
$faq_title = $module->getProjectSetting('faq-title');
$faq_title_tab = $module->getProjectSetting('faq-title-tab');

$faqs = \REDCap::getData(array('project_id'=>$module->getProjectId()),'array');

$help_category_aux = \REDCap::getDataDictionary($module->getProjectId(), 'array', false, 'help_category')['help_category']['select_choices_or_calculations'];
$help_category_aux = explode('|',$help_category_aux);
$help_category = array();
foreach ($help_category_aux as $help){
    $values = explode(',',$help);
    $help_category[trim($values[0])]= trim($values[1]);
}

$help_tab_aux = \REDCap::getDataDictionary($module->getProjectId(), 'array', false, 'help_tab')['help_tab']['select_choices_or_calculations'];
$help_tab_aux = explode('|',$help_tab_aux);
$help_tab = array();
foreach ($help_tab_aux as $help){
    $values = explode(',',$help);
    $help_tab[trim($values[0])]= trim($values[1]);
}

#FIRST PAGE
$first_page = "";
$count_tabs = 0;
foreach ($help_tab as $index=>$tab) {
    $count_tabs++;
    $first_page .= "<table style='width: 100%;'><tr style='background-color: lightgray;' align='center'><td><p>".$tab."</p></td></tr>";
    foreach ($help_category as $category_id => $category_value) {
        $category_count = 0;
        foreach ($faqs as $event) {
            foreach ($event as $faq) {
                if($index == $faq['help_tab']) {
                    if ($faq['help_category'] == $category_id && $faq['help_show_y'] != "0") {
                        if ($category_count == 0) {
                            $first_page .= '<tr><td style="text-decoration: underline;font-style: italic"><p>' . $help_category[$faq['help_category']] . '</p></td></tr>';
                        }
                        $category_count++;
                        $collapse_id = "category_" . $category_id . "_question_" . $category_count."_tab_".$index;

                        $first_page .= '<tr><td><ul><li>' . $faq['help_question'] . '</li></ul></td></tr>';
                        $first_page .= '<tr><td style="padding-left: 40px;">' . $faq['help_answer'] . '</td></tr>';
                        if($faq['help_image'] != ""){
                            $first_page .= '<tr><td style="padding-left: 40px;"><br/>' .\Vanderbilt\FaqMenuExternalModule\printFile($module, $faq['help_image'], 'imgpdf').'<br/></td></tr>';
                        }
                        if($faq['help_document'] != ""){
                            $first_page .= '<tr><td style="padding-left: 40px;">' .\Vanderbilt\FaqMenuExternalModule\printFile($module, $faq['help_document'], 'doc'). '</td></tr>';
                        }
                        if($faq['help_document2'] != ""){
                            $first_page .= '<tr><td style="padding-left: 40px;">' .\Vanderbilt\FaqMenuExternalModule\printFile($module, $faq['help_document2'], 'doc'). '</td></tr>';
                        }

                        if ($faq['help_videoformat'] == '1') {
                            $first_page .= '</br><div><iframe class="commentsform" id="redcap-video-frame" name="redcap-video-frame" src="' . $faq['help_videolink'] . '" width="520" height="345" frameborder="0" allowfullscreen style="display: block; margin: 0 auto;"></iframe></div>';
                        } else if ($faq['help_videoformat'] == '2'){
                            $first_page .= '</br><div class="help_embedcode">' . $faq['help_embedcode'] . '</div>';
                        }
                    }
                }
            }
        }
    }
    $first_page .= '</table>';
    if(count($help_tab) > $count_tabs){
        $first_page .= '<div style="page-break-before: always;"></div>';
    }
}

$first_page .= "</span></td></tr></table>";

$page_num = '<style>.footer .page-number:after { content: counter(page); } .footer { position: fixed; bottom: 0px;color:grey }a{text-decoration: none;}</style>';

$html_pdf = "<html><body style='font-family:\"Calibri\";font-size:10pt;'>".$page_num
    ."<div class='footer' style='left: 600px;'><span class='page-number'>Page </span></div>"
    ."<div class='mainPDF'><table style='width: 100%;padding-top: 30px'><tr><td align='center'>".\Vanderbilt\FaqMenuExternalModule\printFile($module,$faq_logo,'logo')."</td></tr></table></div>"
    ."<div class='mainPDF'><table style='width: 100%;'><tr><td align='center'>".$faq_title."</td></tr></table>".$first_page."</div>"
    ."</body></html>";

$filename = str_replace(" ","_",$faq_title_tab)."_".date("Y-m-d_hi",time());

//DOMPDF
$reportHash = $filename;
$storedName = md5($reportHash);
$filePath = EDOC_PATH.$storedName;

$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($html_pdf);
$dompdf->setPaper('A4', 'portrait');
$options = $dompdf->getOptions();
$options->setChroot(EDOC_PATH);
$dompdf->setOptions($options);
ob_start();
$dompdf->render();
//#Download option
$dompdf->stream($filename);
$filesize = file_put_contents(EDOC_PATH.$storedName, ob_get_contents());

?>