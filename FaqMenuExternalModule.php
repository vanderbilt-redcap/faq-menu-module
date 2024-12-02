<?php
namespace Vanderbilt\FaqMenuExternalModule;

use Exception;
use REDCap;
use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;

class FaqMenuExternalModule extends AbstractExternalModule{
    public function loadREDCapJS(){
        if (method_exists(get_parent_class($this), 'loadREDCapJS')) {
            parent::loadREDCapJS();
        } else {
            ?>
            <script src='<?=APP_PATH_WEBROOT?>Resources/webpack/js/bundle.js'></script>
            <?php
        }
    }
}