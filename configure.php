<?php
$faq_show = $module->getProjectSetting('faq-show');
$faq_category = $module->getProjectSetting('faq-category');
$faq_question = $module->getProjectSetting('faq-question');
$faq_answer = $module->getProjectSetting('faq-answer');
$faq_image = $module->getProjectSetting('faq-image');
$faq_video_embedcode = $module->getProjectSetting('faq-video-embedcode');
$faq_video_videolink = $module->getProjectSetting('faq-video-videolink');
$faq_description = $module->getProjectSetting('faq-description');
$faq_title = $module->getProjectSetting('faq-title');
$faq_favicon = $module->getProjectSetting('faq-favicon');

$faqs = \REDCap::getData(array('project_id'=>$module->getProjectId()),'array');

$help_category_aux = \REDCap::getDataDictionary($module->getProjectId(), 'array', false, $faq_category)[$faq_category]['select_choices_or_calculations'];
$help_category_aux = explode('|',$help_category_aux);
$help_category = array();
foreach ($help_category_aux as $help){
    $values = explode(',',$help);
    $help_category[trim($values[0])]= trim($values[1]);
}

include_once(__DIR__ . "/functions.php");
?>

<script type="text/javascript" src="<?=$module->getUrl('js/jquery-3.3.1.min.js')?>"></script>
<script type="text/javascript" src="<?=$module->getUrl('js/bootstrap.min.js')?>"></script>

<link rel="stylesheet" type="text/css" href="<?=$module->getUrl('css/bootstrap.min.css')?>">
<link rel="stylesheet" type="text/css" href="<?=$module->getUrl('css/style.css')?>">

<link rel="icon" href="<?=$module->getUrl(getImageToDisplay($faq_favicon))?>">

<title><?=$faq_title?></title>

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

                    var filterResult = $('.searchable .panel').filter(function() {
                        return regex.test($(this).text());
                    })

                    $('.faqHeader').hide();
                    console.log(filterResult)
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
<div class="container" style="margin-top: 60px">
    <h3><?=$faq_title?></h3>
    <p class="hub-title"><?=$faq_description?></p>
</div>

<div class="container">
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

<div class="container">
    <div class="panel-group searchable" id="accordion">
        <?php
        if(!empty($faqs)) {
            foreach ($faqs as $event) {
                foreach ($help_category as $category_id => $category_value) {
                    $category_count = 0;
                    foreach ($event as $faq) {
                        if ($faq[$faq_category] == $category_id && $faq[$faq_show] != "0") {
                            if ($category_count == 0) {
                                echo '<div class="faqHeader">' . $help_category[$faq[$faq_category]] . '</div>';
                            }
                            $category_count++;
                            $collapse_id = "category_" . $category_id . "_question_" . $category_count;

                            echo '<div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#' . $collapse_id . '">' . $faq[$faq_question] . '</a>
                                        </h4>
                                    </div>
                                    <div id="' . $collapse_id . '" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div>' . $faq[$faq_answer] . '</div>';

                            if ($faq[$faq_image] != '') {
                                $sql = "SELECT stored_name,doc_name,doc_size FROM redcap_edocs_metadata WHERE doc_id=" . $faq[$faq_image];
                                $q = db_query($sql);

                                if ($error = db_error()) {
                                    die($sql . ': ' . $error);
                                }

                                while ($row = db_fetch_assoc($q)) {
                                    $url = 'downloadFile.php?sname='.$row['stored_name'].'&file='. urlencode($row['doc_name']);
                                    echo '</br><div><img src="'.$module->getUrl($url).'" style="display: block; margin: 0 auto;"></div>';
                                }
                            }

                            if ($faq['help_videoformat'] == '1') {
                                echo '</br><div><iframe class="commentsform" id="redcap-video-frame" name="redcap-video-frame" src="' . $faq[$faq_video_videolink] . '" width="520" height="345" frameborder="0" allowfullscreen style="display: block; margin: 0 auto;"></iframe></div>';
                            }else{
                                echo '</br><div class="help_embedcode">' . $faq[$faq_video_embedcode] . '</div>';
                            }

                            echo '</div>
                                </div>
                            </div>';
                        }
                    }
                }
            }
        }
        ?>
    </div>
</div>
