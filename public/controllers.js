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
            gapi.auth.signOut();

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

app.controller('GooglePlusController', ['$scope', '$http', '$localStorage', 'Authentication', '$location',
    function($scope, $http, $localStorage, Authentication, $location) {
        // Process user info.
        // userInfo is a JSON object.
        $scope.processUserInfo = function(userInfo) {
            var fd = {
                'google_id': userInfo['id'],
                'name': userInfo['displayName'],
                'email': userInfo['emails'][0]['value']
            };

            $http.defaults.headers.common.Authorization = $localStorage.token;

            $http.post('api/auth/googleplus', fd )
                .success(function(response) {
                    $localStorage.user = response.user;

                    Authentication.refresh();

                    $location.path('settings');
                })
                .error(function() {
                    $localStorage.token = null;

                    Authentication.refresh();
                });
        };

        // When callback is received, process user info.
        $scope.userInfoCallback = function(userInfo) {
            $scope.$apply(function() {
                $scope.processUserInfo(userInfo);
            });
        };

        // Request user info.
        $scope.getUserInfo = function() {
            gapi.client.request(
                {
                    'path':'/plus/v1/people/me',
                    'method':'GET',
                    'callback': $scope.userInfoCallback
                }
            );
        };

        // Here we do the authentication processing and error handling.
        // Note that authResult is a JSON object.
        $scope.processAuth = function(authResult) {
            // Do a check if authentication has been successful.
            if (authResult['access_token']) {
                // Successful sign in.
                $scope.signedIn = true;

                $localStorage.token = authResult['access_token'];

                $scope.getUserInfo();

            } else if (authResult['error']) {
                // Error while signing in.
                $scope.signedIn = false;

                // Report error.
            }
        };

        // When callback is received, we need to process authentication.
        $scope.signInCallback = function(authResult) {
            $scope.$apply(function() {
                $scope.processAuth(authResult);
            });
        };

        // Render the sign in button.
        $scope.renderSignInButton = function() {
            gapi.signin.render('signInButton',
                {
                    'callback': $scope.signInCallback, // Function handling the callback.
                    'clientid': '800905310607-si9c22qkj0k5g226g5a8868rcff029tu.apps.googleusercontent.com', // CLIENT_ID from developer console which has been explained earlier.
                    'requestvisibleactions': '', // Visible actions, scope and cookie policy wont be described now,
                    // as their explanation is available in Google+ API Documentation.
                    'scope': 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email',
                    'cookiepolicy': 'single_host_origin'
                }
            );
        };

        // Start function in this example only renders the sign in button.
        $scope.start = function() {
            $scope.renderSignInButton();
        };

        // Call start function on load.
        $scope.start();
}]);

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
                    if (response.user) {
                        $scope.user = response.user;
                        $localStorage.user = $scope.user;
                        Authentication.refresh();
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

