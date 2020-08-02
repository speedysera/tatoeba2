(function() {
    'use strict';

    angular
        .module('app', ['ngMaterial', 'ngMessages', 'ngCookies', 'ngSanitize'])
        .config(['$mdThemingProvider', '$mdIconProvider', '$httpProvider', '$httpParamSerializerJQLikeProvider', '$cookiesProvider',
                 '$compileProvider', function(
            $mdThemingProvider, $mdIconProvider, $httpProvider, $httpParamSerializerJQLikeProvider, $cookiesProvider, $compileProvider
        ) {
            $compileProvider.debugInfoEnabled(false);
            $compileProvider.commentDirectivesEnabled(false);
            $compileProvider.cssClassDirectivesEnabled(false);
            $mdThemingProvider.theme('default')
                .primaryPalette('green')
                .accentPalette('grey')
                .warnPalette('red',  {'default': '700'});
            // https://stackoverflow.com/questions/12190166/angularjs-any-way-for-http-post-to-send-request-parameters-instead-of-json
            $httpProvider.defaults.transformRequest.unshift($httpParamSerializerJQLikeProvider.$get());
            $httpProvider.defaults.headers.post['Content-Type'] =
                'application/x-www-form-urlencoded; charset=UTF-8';
            $httpProvider.defaults.headers.common['X-Requested-With'] =
                'XMLHttpRequest';
            $httpProvider.defaults.xsrfHeaderName = 'X-CSRF-Token';
            $httpProvider.defaults.xsrfCookieName = 'csrfToken';

            var date = new Date();
            $cookiesProvider.defaults.path = '/';
            $cookiesProvider.defaults.expires = new Date(date.setMonth(date.getMonth() + 1));
        }])
        .filter('urlEncode', function() {
            return function(input) {
                if (input) {
                    return window.encodeURIComponent(input);
                }
                return "";
            };
        })
        .filter('filename', function() {
            return function(input) {
                return input.substring(input.lastIndexOf('/') + 1);
            };
        })
        // https://stackoverflow.com/questions/28851893/angularjs-textaera-enter-key-submit-form-with-autocomplete
        .directive('ngEnter', function() {
            return function(scope, element, attrs) {
                element.bind('keydown', function(e) {
                    if(e.which === 13) {
                        scope.$apply(function(){
                            scope.$eval(attrs.ngEnter, {'e': e});
                        });
                        e.preventDefault();
                    }
                });
            };
        })
        .directive('ngEscape', function() {
            return function(scope, element, attrs) {
                element.bind('keydown', function(e) {
                    if(e.which === 27) {
                        scope.$apply(function(){
                            scope.$eval(attrs.ngEscape, {'e': e});
                        });
                        e.preventDefault();
                    }
                });
            };
        })
        .controller('MenuController', ['$scope', '$mdSidenav', '$mdDialog', function($scope, $mdSidenav, $mdDialog) {
            $scope.toggleMenu = function() {
                $mdSidenav('menu').toggle();
            };

            $scope.showInterfaceLanguageSelection = function() {
                $mdDialog.show({
                    controller: DialogController,
                    clickOutsideToClose: true,
                    templateUrl: get_tatoeba_root_url() + '/angular_templates/interface_language'
                });
            }

            DialogController.$inject = ['$scope', '$mdDialog'];
            function DialogController($scope, $mdDialog) {
                $scope.init = function (data) {
                    $scope.languages = data;
                }

                $scope.close = function() {
                    $mdDialog.cancel();
                };

                $scope.changeInterfaceLang = function(newLang) {
                    // Saving the cookie
                    var date = new Date();
                    date.setMonth(date.getMonth()+1);
                    document.cookie = 'CakeCookie[interfaceLanguage]=' + newLang
                        + '; path=/'
                        + '; expires=' + date.toGMTString();
                    location.reload();
                }
            }
        }])
        .directive('resetButton', function(){
            return function(scope, element, attrs) {
                element.bind('click', function(e) {
                    var target = attrs.target;
                    var text_input = document.getElementById(target);
                    text_input.value = '';
                    text_input.focus();
                });
            };
        });
})();
