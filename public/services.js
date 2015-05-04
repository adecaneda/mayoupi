// Authentication service for user variables
app.service('Authentication', ['$localStorage', '$http', function($localStorage, $http) {
    var auth = {};

    auth.refresh = function() {
        auth.user = $localStorage.user;
        auth.token = $localStorage.token;
        auth.isAdmin = $localStorage.user && $localStorage.user.role == 'admin';
    };

    auth.refresh();

    return auth;
}]);
