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
                administration: true
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
            .state('profile', {
                url: '/settings/profile',
                templateUrl: 'public/users/views/settings/profile.html'
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
                templateUrl: 'public/users/views/authentication/signout.html'
            });
    }
]);

app.run(function ($rootScope, $state, $injector, Authentication, $localStorage, $http) {

    $http.defaults.headers.common.Authorization = $localStorage.token;

    $rootScope.$on("$stateChangeStart", function(event, toState, toParams, fromState, fromParams){
        if (toState.authenticate && !$localStorage.user){
            // User isn’t authenticated
            $state.transitionTo("signin");
            event.preventDefault();

        } else if (toState.administration
            && (!Authentication.user || Authentication.user.role != 'admin')
        ) {
            // User isn’t admin
            $state.transitionTo("home");
            event.preventDefault();
        }
    });

});

