<?php
namespace Vanderbilt\FaqMenuExternalModule;
include_once(__DIR__ . "/functions.php");

$faq_description = $module->getProjectSetting('faq-description');
$faq_title = $module->getProjectSetting('faq-title');
$faq_title_tab = $module->getProjectSetting('faq-title-tab');
$faq_logo = $module->getProjectSetting('faq-logo');
$faq_favicon = $module->getProjectSetting('faq-favicon');
$faq_project = $module->getProjectSetting('faq-project');
$faq_search = $module->getProjectSetting('faq-search');
$faq_pdf = $module->getProjectSetting('faq-pdf');
$faq_privacy = $module->getProjectSetting('faq-privacy');
$project_id = (int)$_REQUEST['pid'];

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
?>

<!-- To scale on mobile add this line -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<script type="text/javascript" src="<?=$module->getUrl('js/jquery-3.3.1.min.js')?>"></script>
<script type="text/javascript" src="<?=$module->getUrl('js/bootstrap.min.js')?>"></script>

<link rel="stylesheet" type="text/css" href="<?=$module->getUrl('css/bootstrap.min.css')?>">
<link rel="stylesheet" type="text/css" href="<?=$module->getUrl('css/style.css')?>">
<link type='text/css' href=<?=$module->getUrl('css/font-awesome.min.css')?> rel='stylesheet' media='screen' />
<link type='text/css' href='<?=$module->getUrl('css/tabs-steps-menu.css')?>' rel='stylesheet' media='screen' />

<link rel="icon" href="<?=$module->getUrl(\Vanderbilt\FaqMenuExternalModule\getImageToDisplay($faq_favicon))?>">

<title><?=$faq_title_tab?></title>


<script>
    $(document).ready(function() {
        (function($) {
            var $form = $('#filter-form');
            var $helpBlock = $("#filter-help-block");

            //Watch for user typing to refresh the filter
            $('#filter').keyup(function() {
                var filter = $(this).val();
                $form.removeClass("has-success has-error");

                if (filter == "") {
                    $helpBlock.text("No filter applied.")
                    $('.searchable .panel').show();
                    $('.faqHeader').show();
                } else {
                    //Close any open panels
                    $('.collapse.in').removeClass('in');

                    //Hide questions, will show result later
                    $('.searchable .panel').hide();

                    var regex = new RegExp(filter, 'i');

                    var filterResult = $('.searchable .tabpanel .panel').filter(function() {
                        return regex.test($(this).text());
                    })

                    $('.faqHeader').hide();
                    if (filterResult) {
                        if (filterResult.length != 0) {
                            $form.addClass("has-success");
                            $helpBlock.text(filterResult.length + " question(s) found.");
                            filterResult.show();
                        } else {
                            $form.addClass("has-error").removeClass("has-success");
                            $helpBlock.text("No questions found.");
                        }

                    } else {
                        $form.addClass("has-error").removeClass("has-success");
                        $helpBlock.text("No questions found.");
                    }
                }
            })

        }($));
    });

    //
    //  This function disables the enter button
    //  because we're using a form element to filter, if a user
    //  pressed enter, it would 'submit' a form and reload the page
    //  Probably not needed here on Codepen, but necessary elsewhere
    //
    $('.noEnterSubmit').keypress(function(e) {
        if (e.which == 13) e.preventDefault();
    });
</script>

<?php
$has_permission = false;

if($faq_privacy == 'public'){
    $has_permission = true;
}else if($faq_privacy == 'main'){
    if(!defined('USERID')){
        echo '<div class="container" style="margin-top: 60px"><div class="alert alert-warning" role="alert">Please log in REDCap to access this FAQ.</div></div>';
        exit;
    }else if(\Vanderbilt\FaqMenuExternalModule\isUserExpiredOrSuspended(USERID, 'user_suspended_time') || \Vanderbilt\FaqMenuExternalModule\isUserExpiredOrSuspended(USERID, 'user_expiration')) {
        echo '<div class="container" style="margin-top: 60px"><div class="alert alert-warning" role="alert">This user is expired or suspended. Please contact an administrator.</div></div>';
        exit;
    }else{
        $q = $module->query("SELECT * FROM `redcap_user_rights` WHERE project_id=? AND username=?",[$project_id, USERID]);
        if ($q->num_rows > 0) {
            $has_permission = true;
        }
    }
}else if($faq_privacy == 'other') {
    if(!defined('USERID')){
        echo '<div class="container" style="margin-top: 60px"><div class="alert alert-warning" role="alert">Please log in REDCap to access this FAQ.</div></div>';
        exit;
    }else if(count($faq_project) == 0) {
        echo '<div class="container" style="margin-top: 60px"><div class="alert alert-warning" role="alert">Please select a project(s) to give permissions to.</div></div>';
        exit;
    }else if(isUserExpiredOrSuspended(USERID, 'user_suspended_time') || isUserExpiredOrSuspended(USERID, 'user_expiration')) {
        echo '<div class="container" style="margin-top: 60px"><div class="alert alert-warning" role="alert">This user is expired or suspended. Please contact an administrator.</div></div>';
        exit;
    }else{
        foreach ($faq_project as $project) {
            $q = $module->query("SELECT * FROM `redcap_user_rights` WHERE project_id=? AND username=?",[$project, USERID]);
            if ($q->num_rows > 0) {
                $has_permission = true;
            }
        }
    }
}else{
    echo '<div class="container" style="margin-top: 60px"><div class="alert alert-warning" role="alert">This FAQ has not yet been set up. Please go to the “<strong>External Modules</strong>” menu and configure the FAQ Builder.</div></div>';
    exit;
}





if(!$has_permission){
    echo '<div class="container" style="margin-top: 60px"><div class="alert alert-warning" role="alert">You don\'t have permissions to access this FAQ. Please contact an administrator.</div></div>';
    exit;
}

#Can see the FAQ Builder
if($has_permission){
    if(array_key_exists('upload_success',$_REQUEST)){
        echo '<div class="container" style="margin-top: 60px"><div class="alert alert-success" role="alert">Data Dictionary successfully uploaded.</div></div>';
    }

    if(count($faqs) == 0) {
        echo '<div class="container" style="margin-top: 60px">
            <div class="alert alert-warning" role="alert">
                <span style="line-height: 3;">There is no data in the current project. Please create some records to populate the FAQ.</span>
            </div>
          </div>';
    }
    ?>


    <?php
    if($faq_logo != ""){
        ?>
        <div class="container top-screen">
            <?php echo \Vanderbilt\FaqMenuExternalModule\printFile($module,$faq_logo,'img');?>
        </div>
    <?php } ?>

    <?php
    if($faq_title != "" || $faq_description != ""){
        ?>
        <div class="container" style="margin-top: 60px">
            <h3><?=filter_tags($faq_title)?></h3>
            <p class="hub-title"><?=filter_tags($faq_description)?></p>
        </div>
    <?php } ?>

    <?php if(count($faqs) > 0 && $faq_pdf == "Y") {?>
        <div class="container em-faqbuilder-tab-hidden-mobile">
            <ul class="list-inline pull-right" style="    padding-right: 10%;">
                <li><a href="<?=$module->getUrl('download_PDF.php')?>" class="btn btn-default saveAndContinue" id="save_and_stay" name="save_and_stay" ><span class="fa fa-arrow-down"></span> PDF</a></li>
            </ul>
        </div>
    <?php } ?>

    <?php if(count($faqs) > 0 && $faq_search == "Y") {?>
        <div class="container" style="margin-top: 20px">
            <div class="form-group" id="filter-form">
                <label for="filter">
                    Search for a Question
                </label>
                <input id="filter" type="text" class="form-control noEnterSubmit" placeholder="Enter a keyword or phrase" />
                <small>
        <span id="filter-help-block" class="help-block">
          No filter applied.
        </span>
                </small>
            </div>
        </div>
    <?php } ?>

    <?php if(count($help_tab)>0){ ?>
        <div class="container" style="margin-top: 20px">
            <ul class="nav nav-tabs">
                <?php
                $count = 0;
                foreach ($help_tab as $index=>$tab){
                    $active = '';
                    if($count == 0){
                        $active = 'active';
                    }
                    echo '<li class="nav-item '.htmlentities($active,ENT_QUOTES).'"><a data-toggle="tab" href="#'.htmlentities($index,ENT_QUOTES).'">'.filter_tags($tab).'</a></li>';
                    $count++;
                }
                ?>
            </ul>
        </div>
    <?php } ?>

    <div class="container">
        <div class="panel-group searchable" id="accordion">
            <?php
            if(count($faqs) > 0) {
                echo '<div class="tab-content">';
                $count = 0;
                foreach ($help_tab as $index=>$tab) {
                    $active = '';
                    if($count == 0){
                        $active = 'active';
                    }
                    echo '<div id="' . $index . '" class="tabpanel tab-pane fade in '.htmlentities($active,ENT_QUOTES).'" role="tabpanel">';
                    echo '<div class="panel-group searchable" id="accordion-'.$index.'">';
                    foreach ($help_category as $category_id => $category_value) {
                        $category_count = 0;
                        foreach ($faqs as $event) {
                            foreach ($event as $faq) {
                                if($index == $faq['help_tab']) {
                                    if ($faq['help_category'] == $category_id && $faq['help_show_y'] != "0") {
                                        if ($category_count == 0) {
                                            echo '<div class="faqHeader">' . htmlentities($help_category[$faq['help_category']],ENT_QUOTES) . '</div>';
                                        }
                                        $category_count++;
                                        $collapse_id = "category_" . $category_id . "_question_" . $category_count."_tab_".$index;

                                        echo '<div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#' . htmlentities($collapse_id,ENT_QUOTES) . '">' . filter_tags($faq['help_question']) . '</a>
                                        </h4>
                                    </div>
                                    <div id="' . htmlentities($collapse_id,ENT_QUOTES) . '" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div>' . filter_tags($faq['help_answer']) . '</div>';


                                        echo filter_tags(\Vanderbilt\FaqMenuExternalModule\printFile($module, $faq['help_image'], 'img'));
                                        echo filter_tags(\Vanderbilt\FaqMenuExternalModule\printFile($module, $faq['help_document'], 'doc'));
                                        echo filter_tags(\Vanderbilt\FaqMenuExternalModule\printFile($module, $faq['help_document2'], 'doc'));

                                        if ($faq['help_videoformat'] == '1') {
                                            echo '</br><div><iframe class="commentsform" id="redcap-video-frame" name="redcap-video-frame" src="' . htmlentities($faq['help_videolink'],ENT_QUOTES) . '" width="520" height="345" frameborder="0" allowfullscreen style="display: block; margin: 0 auto;"></iframe></div>';
                                        } else {
                                            echo '</br><div class="help_embedcode">' . htmlentities($faq['help_embedcode'],ENT_QUOTES) . '</div>';
                                        }

                                        echo '</div>
                                            </div>
                                        </div>';
                                    }
                                }
                            }
                        }
                    }
                    echo '</div>';
                    echo '</div>';
                    $count++;
                }
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <?php if(count($faqs) > 0 && $faq_pdf == "Y") {?>
        <div class="container em-faqbuilder-tab-hidden-desktop" style="padding-bottom:30px;padding-top:20px;">
            <a href="<?=$module->getUrl('download_PDF.php')?>" class="btn btn-default saveAndContinue" style="width: 100%;"><span class="fa fa-arrow-down"></span> PDF</a>
        </div>
    <?php } ?>
<?php } ?>