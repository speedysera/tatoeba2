<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

use Cake\Core\Configure;
use App\Model\CurrentUser;

// Detecting language for "browse by language"
$session = $this->request->getSession();
$currentLanguage = $session->read('browse_sentences_in_lang');
$showTranslationsInto = $session->read('show_translations_into_lang');
$filteredLanguage = $session->read('vocabulary_requests_filtered_lang');

if (empty($currentLanguage)) {
    $currentLanguage = $session->read('random_lang_selected');
}
if (empty($currentLanguage) || $currentLanguage == 'und') {
    $currentLanguage = Configure::read('Config.language');
}
if (empty($showTranslationsInto)) {
    $showTranslationsInto = 'none';
}

// array containing the elements of the menu : $title => $route
$menuElements = array(
    /* @translators: menu on the top (verb) */
    __('Browse') => array(
        "sub-menu" => array(
            /* @translators: menu item on the top (verb) */
            __('Show random sentence') => array(
                "controller" => "sentences",
                "action" => "show",
                "random"
            ),
            /* @translators: menu item on the top (verb) */
            __('Browse by language') => array(
                "controller" => "sentences",
                "action" => "show_all_in",
                $currentLanguage, $showTranslationsInto
            ),
            /* @translators: menu item on the top (verb) */
            __('Browse by list') => array(
                "controller" => "sentences_lists",
                "action" => "index"
            ),
            /* @translators: menu item on the top (verb) */
            __('Browse by tag') => array(
                "controller" => "tags",
                "action" => "view_all"
            ),
            /* @translators: menu item on the top (verb) */
            __('Browse audio') => array(
                "controller" => "audio",
                "action" => "index"
            )
        )
    ),
    /* @translators: menu on the top (verb) */
    __('Contribute') => array(
        'hidden' => !CurrentUser::isMember(),
        "sub-menu" => array(
            /* @translators: menu item on the top (verb) */
            __('Add sentences') => array(
                "controller" => "sentences",
                "action" => "add"
            ),
            /* @translators: menu item on the top (verb) */
            __('Translate sentences') => array(
                "controller" => "activities",
                "action" => "translate_sentences"
            ),
            /* @translators: menu item on the top (verb) */
            __('Adopt sentences') => array(
                "controller" => "activities",
                "action" => "adopt_sentences",
                $currentLanguage
            ),
            /* @translators: menu item on the top (verb) */
            __('Improve sentences') => array(
                "controller" => "activities",
                "action" => "improve_sentences"
            ),
            /* @translators: menu item on the top (verb) */
            __('Discuss sentences') => array(
                "controller" => "sentence_comments",
                "action" => "index"
            ),
            /* @translators: menu item on the top (verb) */
            __('Add vocabulary request') => array(
                "controller" => "vocabulary",
                "action" => "add_sentences",
                $filteredLanguage
            ),
        )
    ),
    /* @translators: menu on the top (verb) */
    __('Community') => array(
        "sub-menu" => array(
            /* @translators: menu item on the top (verb) */
            __('Wall') => array(
                "controller" => "wall",
                "action" => "index"
            ),
            /* @translators: menu item on the top (verb) */
            __('List of all members') => array(
                "controller" => "users",
                "action" => "all"
            ),
            /* @translators: menu item on the top (verb) */
            __('Languages of members') => array(
                "controller" => "stats",
                "action" => "users_languages"
            ),
            /* @translators: menu item on the top (verb) */
            __('Native speakers') => array(
                "controller" => "stats",
                "action" => "native_speakers"
            )
        )
    )
);

if (!isset($htmlDir)) {
    $htmlDir = null;
}
$menuPositionMode = $htmlDir == 'rtl' ? 'target-right target' : 'target target';
$sidenavPosition = $htmlDir == 'rtl' ? 'md-sidenav-right' : 'md-sidenav-left';
?>

<div id="top_menu_container" ng-controller="MenuController">
    <md-toolbar id="top_menu" md-colors="{background: 'grey-800'}" layout="column" layout-gt-xs="row" layout-align-gt-xs="start center" flex ng-cloak>
        <?= $this->element('header'); ?>
        
        <div flex hide-xs hide-sm>
        <?php
        foreach ($menuElements as $title => $data) {
            if (isset($data['hidden']) && $data['hidden']) {
                continue;
            }
            ?>
            <div class="dropdown">
                <div class="label"><?= $title ?> <md-icon>expand_more</md-icon></div>

                <div class="dropdown-content">
                <?php foreach ($data['sub-menu'] as $title2 => $route) { ?>
                    <div class="item"><?= $this->Html->link($title2, $route); ?></div>
                <?php } ?>
                </div>
            </div>
            <?php
        }
        ?>
        </div>

        <div id="user_menu" ng-cloak>
            <?php
            // User menu
            if (!$session->read('Auth.User.id')) {
                $action = $this->request->getParam('action');
                $controller = $this->request->getParam('controller');
                $isOnLoginPage = ($controller == 'Users' && $action == 'login');

                if (!$isOnLoginPage) {
                    echo $this->element('login');
                }
            } else {
                echo $this->element('space', ['htmlDir' => $htmlDir]);
            }
            ?>
        </div>
        
        <?php 
        if (!CurrentUser::isMember()) {
            echo $this->element('ui_language_button', [
                'displayOption' => 'hide-xs'
            ]);
        }
        ?>
    </md-toolbar>

    <md-sidenav class="sidenav-menu <?= $sidenavPosition ?>" md-component-id="menu" md-disable-scroll-target="body" ng-cloak>
        <md-content>
            <?php
            $name = __('Tatoeba');
            
            $logo = $this->Html->image(
                IMG_PATH . 'tatoeba.svg',
                array(
                    'width' => 32,
                    'height' => 32,
                    'title' => $name

                )
            );
            ?>
            <div layout="row" layout-align="start center" layout-padding>
                <md-button class="md-icon-button" ng-click="toggleMenu()">
                    <md-icon>menu</md-icon>
                </md-button>
                <div layout="row" layout-align="center center">
                    <span><?= $logo ?></span>
                    <span class="tatoeba-name"><?= $name ?></span>
                </div>
            </div>

            <md-list>
            <?php
            foreach ($menuElements as $title => $data) {
                if (isset($data['hidden']) && $data['hidden']) {
                    continue;
                }
                ?>
                <md-subheader><?= $title ?></md-subheader>
                <?php
                // Sub-menu
                if (!empty($data['sub-menu'])) {
                    foreach ($data['sub-menu'] as $title2 => $route2) {
                        ?>
                        <md-list-item href="<?= $this->Url->build($route2) ?>">
                            <p>
                            <md-icon>chevron_right</md-icon>
                            <?= $title2 ?>
                            </p>
                        </md-list-item>
                        <?php
                    }
                    
                }
                
            }
            ?>
            </md-list>
        </md-content>

    </md-sidenav>
</div>


