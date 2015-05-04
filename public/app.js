// create the module and name it app
var app = angular.module('app', ['ui.router', 'ngStorage']);

// Authentication service for user variables
app.service('Authentication', ['$localStorage', '$http', function($localStorage, $http) {
    var auth = {
        user: $localStorage.user,
        token: $localStorage.token,
        isAdmin: $localStorage.user && $localStorage.user.role == 'admin'
    };
    auth.refresh = function() {
        auth.user = $localStorage.user;
        auth.token = $localStorage.token;
    };
    return auth;
}]);

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

// Header controller
app.controller('HeaderController', ['$scope', 'Authentication', '$localStorage',
    function($scope, Authentication, $localStorage) {
        $scope.authentication = Authentication;
}]);

app.controller('AuthenticationController', ['$scope', '$http', '$location', '$localStorage', 'Authentication',
    function($scope, $http, $location, $localStorage, Authentication) {
        $scope.authentication = Authentication;

        // If user is signed in then redirect back home
        if ($localStorage.token) {
            $location.path('/');
        }

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
                    $localStorage.token = response.token;
                    $localStorage.user = response.user;

                    Authentication.refresh();

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
                    //@todo use $scope.me() for user
                    $localStorage.token = response.token;
                    $localStorage.user = response.user;

                    Authentication.refresh();

                    // And redirect to the index page
                    $location.path('/');
                } else {
                    $scope.error = 'Authentication failed!';
                }

            }).error(function(response) {
                    $scope.error = response.message;
                });
        };

        $scope.me = function() {
            $http.get('api/auth/me')
                .success(function(response) {
                    $localStorage.user = response.user;

                    Authentication.refresh();
                }.error(function(response){
                }));
        };

        $scope.signout = function() {
            $http.get('api/auth/logout')
                .success(function(/*response*/) {
                    $localStorage.token = null;
                    $localStorage.user = null;

                    Authentication.refresh();

                    // And redirect to the index page
                    $location.path('/signin');
                });
        };
    }
]);

app.run(function ($rootScope, $state, $injector, Authentication, $localStorage) {

    $injector.get("$http").defaults.transformRequest = function(data, headersGetter) {
        if ($localStorage.token) {
            headersGetter()['Authorization'] = "Bearer " + $localStorage.token;
        }
        if (data) {
            return data;
        }
    };

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

