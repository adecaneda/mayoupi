// create the module and name it app
var app = angular.module('app', ['ui.router', 'ngStorage']);

// Setting up route
app.config(['$stateProvider', '$urlRouterProvider', '$httpProvider',
    function($stateProvider, $urlRouterProvider, $httpProvider) {

        // Send to home if the URL was not found
        $urlRouterProvider.otherwise("/home");

        $stateProvider

            // routes for the home section
            .state('home', {
                url  : '/home',
                templateUrl : 'public/home/index.html'
            })

            // routes for the admin section
            .state('admin', {
                url : '/admin',
                templateUrl : 'public/admin/views/admin.html',
                administration: true,
                authenticate: true
            })
            .state('admin.home', {
                url: '',
                templateUrl: 'public/admin/views/admin-home.html'
            })
            .state('admin.users', {
                url: '/users',
                templateUrl: 'public/admin/views/admin-users.html',
                controller: function($scope, $http) {
                    if (!$scope.users) {
                        $http({method: 'GET', url: './api/users'}).
                            success(function(data, status) {
                                $scope.users = angular.fromJson(data).users;
                            }).
                            error(function(data, status, headers, config) {
                                $scope.users = [];
                                $scope.status = status;
                            });
                    }
                }
            })

            // routes for the user section
            .state('settings', {
                url: '/settings',
                templateUrl: 'public/users/views/settings/settings.html',
                authenticate: true
            })
            .state('settings.avatar', {
                url: '/avatar',
                templateUrl: 'public/users/views/settings/settings-avatar.html',
                authenticate: true
            })
            .state('settings.accept_terms', {
                url: '/accept_terms',
                templateUrl: 'public/users/views/settings/settings-accept-terms.html',
                authenticate: true
            })
            .state('signup', {
                url: '/signup',
                templateUrl: 'public/users/views/authentication/signup.html'
            })
            .state('signin', {
                url: '/signin',
                templateUrl: 'public/users/views/authentication/signin.html'
            })
            .state('signout', {
                url: '/signout',
                templateUrl: 'public/users/views/authentication/signout.html',
                authenticate: true
            });
    }
]);

app.run(function ($rootScope, $state, $injector, Authentication, $localStorage, $http, $location) {

//    $http.defaults.headers.common.Authorization = $localStorage.token;

    $rootScope.$on("$stateChangeStart", function(event, toState, toParams, fromState, fromParams){
        $http.defaults.headers.common.Authorization = $localStorage.token;

        if (toState.name === 'signout') {

        // restricted area for users
        } else if (toState.authenticate && !$localStorage.user){
            // User is not authenticated
            $state.transitionTo('signin');
            event.preventDefault();

        // user must accept the terms and conditions
        } else if (Authentication.user
            && parseInt(Authentication.user.tac_accepted) == 0
            && toState.name !== 'settings.accept_terms'
        ) {
            $state.transitionTo('settings.accept_terms');
            event.preventDefault();

        // only admin users can access the administration area
        } else if (toState.administration
            && (!Authentication.user || Authentication.user.role != 'admin')
        ) {
            // User is not admin
            event.preventDefault();
            $state.transitionTo('');

        } else if (toState.name === 'settings') {
            $state.transitionTo('settings.avatar');
            event.preventDefault();
        }
    });

});

