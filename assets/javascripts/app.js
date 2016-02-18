angular.module('media42', [
    'angularFileUpload'
]);

angular.module('media42').config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);
