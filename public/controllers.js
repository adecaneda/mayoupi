// Header controller
app.controller('HeaderController', ['$scope', 'Authentication', '$localStorage',
    function($scope, Authentication, $localStorage) {
        $scope.authentication = Authentication;
    }]);

// Authentication controller
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

// User settings controller
app.controller('SettingsController', ['$scope', '$http', '$location', '$localStorage', 'Authentication',
    function($scope, $http, $location, $localStorage, Authentication) {
        $scope.user = Authentication.user;
        $scope.formTerms = {};

        /**
         * Method to upload an avatar.
         *
         * @param files
         */
        $scope.uploadAvatar = function(files) {
            var fd = new FormData();
            fd.append('file', files[0]);
            $http.post('api/users/upload-avatar', fd, {
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined}
                })
                .success(function(response) {
                    if (response.url) {
                        $scope.user._avatar.url = response.url;
                    } else {
                        alert('Error uploading avatar');
                    }
                })
                .error(function() {
                    alert('Error uploading avatar');
                });
        };

        /**
         * Method to accept the Terms and Conditions policy
         */
        $scope.acceptTerms = function() {
            // form validation
            if ($scope.formTerms.tac_accepted !== true) {
                $scope.error = 'You must accept the terms';
                return;
            }

            $http.post('api/users/accept-terms')
                .success(function(response) {
                    if (response.tac_accepted) {
                        $scope.user.tac_accepted = response.tac_accepted;
                        $location.path('settings.avatar')
                    } else {
                        alert('Error accepting terms and conditions');
                    }
                })
                .error(function() {
                    alert('Error accepting terms and conditions');
                });
        };
    }
]);

