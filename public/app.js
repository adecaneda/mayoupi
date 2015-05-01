// create the module and name it app
var app = angular.module('app', ['ui.router']);


// Authentication service for user variables
app.service('Authentication', ['$window', function($window) {
    var auth = {
        user: $window.user
    };
    return auth;
}]);

// Setting up route
app.config(['$stateProvider', '$urlRouterProvider',
    function($stateProvider, $urlRouterProvider) {

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
                templateUrl : 'admin/views/admin.html',
                administration: true
            })
            .state('admin.home', {
                url: '',
                templateUrl: 'admin/views/admin-home.html'
            })
            .state('admin.users', {
                url: '/users',
                templateUrl: 'admin/views/admin-users.html',
                controller: function($scope, $http) {
                    if (!$scope.users) {
                        $http({method: 'GET', url: './api/users'}).
                            success(function(data, status) {
                                $scope.users = angular.fromJson(data).users;
                                console.log(data);
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

// Header controller
app.controller('HeaderController', ['$scope', 'Authentication',
    function($scope, Authentication) {
        $scope.authentication = Authentication;
}]);

app.controller('AuthenticationController', ['$scope', '$http', '$location', 'Authentication',
    function($scope, $http, $location, Authentication) {
        $scope.authentication = Authentication;

        // If user is signed in then redirect back home
        if ($scope.authentication.user) $location.path('/');

        $scope.signup = function() {
            // form validation
            if (!$scope.formData.name
                || !$scope.formData.email
                || !$scope.formData.password
                || !$scope.formData.tac_accepted
            ) {
                $scope.error = 'All fields are mandatory';
                return;
            }

            // make the request if form is validated
            $http({
                method: 'POST',
                url: 'api/auth/register',
                data:  'name=' + $scope.formData.name +
                        '&email=' + $scope.formData.email +
                        '&tac_accepted=' + $scope.formData.tac_accepted +
                        '&password=' + $scope.formData.password,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success(function(response) {
                if (response.user) {
                    // If successful we assign the response to the global user model
                    $scope.authentication.user = response.user;

                    // And redirect to the index page
                    $location.path('/');
                } else {
                    $scope.error = 'Authentication failed!';
                }
            }).error(function(response) {
                $scope.error = response.message;
            });
        };

        $scope.signin = function() {
            $http({
                method: 'POST',
                url: 'api/auth/login',
                data:  'email=' + $scope.credentials.email +
                        '&password=' + $scope.credentials.password,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}

            }).success(function(response) {
                // If successful we assign the response to the global user model
                if (response.user) {
                    $scope.authentication.user = response.user;

                    // And redirect to the index page
                    $location.path('/');
                } else {
                    $scope.error = 'Authentication failed!';
                }

            }).error(function(response) {
                    $scope.error = response.message;
                });
        };

        $scope.signout = function() {
            $http.get('api/auth/logout')
                .success(function(/*response*/) {
                    $scope.authentication.user = null;

                    // And redirect to the index page
                    $location.path('/signin');
                });
        }
    }
]);

app.run(function ($rootScope, $state, Authentication) {
    $rootScope.$on("$stateChangeStart", function(event, toState, toParams, fromState, fromParams){
        if (toState.authenticate && !Authentication.user){
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