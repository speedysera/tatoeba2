<?php
use App\Lib\LanguagesLib;
use Cake\Core\Configure;

$lang = Configure::read('Config.language');
$configUiLanguages = Configure::read('UI.languages');
$languages = array();

foreach ($configUiLanguages as $langs) {
    list($isoCode, $suffix, $name) = $langs;
    $fullIsoCode = LanguagesLib::languageTag($isoCode, $suffix);
    $languages[] = array(
        'text' => $name,
        'value' => $isoCode,
        'lang' => $fullIsoCode,
        'dir' => LanguagesLib::getLanguageDirection($isoCode),
    );
}

usort(
    $languages,
    function($a, $b) {
        return strnatcmp($a['text'], $b['text']);
    }
);

$languagesJSON = h(json_encode($languages));
?>

<md-dialog aria-label="<?= __('Interface language') ?>" ng-cloak ng-init="init(<?= $languagesJSON ?>)">
    <md-toolbar>
        <div class="md-toolbar-tools">
            <h2 flex><?= __('Interface language'); ?></h2>
            <md-button class="md-icon-button" ng-click="close()">
              <md-icon>close</md-icon>
            </md-button>
        </div>
    </md-toolbar>

    <md-dialog-content>
        <div layout-gt-xs="row" layout-wrap style="max-width: 1000px">
            <div ng-repeat="lang in languages" layout="column" flex-xs="100" flex-sm="50" flex-gt-sm="25">
                <md-button ng-click="changeInterfaceLang(lang.value)">{{lang.text}}</md-button>
            </div>
        </div>
    </md-dialog-content>
</md-dialog>