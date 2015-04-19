
// create the module and name it mayoupiApp
var mayoupiApp = angular.module('mayoupiApp', ['ui.router']);

// configure our routes
mayoupiApp.config(function($stateProvider, $urlRouterProvider) {

    $urlRouterProvider.otherwise('/home');

    $stateProvider
        // route for the home page
        .state('home', {
            url  : '/home',
            templateUrl : 'partials/home.html'
        })

        // route for the admin page
        .state('admin', {
            url : '/admin',
            templateUrl : 'partials/admin.html'
        })

        .state('admin.home', {
            url: '',
            templateUrl: 'partials/admin-home.html'
        })
            // nested list with custom controller
        .state('admin.users', {
            url: '/users',
            templateUrl: 'partials/admin-users.html',
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
        });
});