angular.module('media42', [
    'angularFileUpload'
]);

angular.module('media42').config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);
;
angular.module('media42')
    .controller('FileSelectorController', ['$scope', '$attrs', 'jsonCache', '$modal', 'MediaService', function ($scope, $attrs, jsonCache, $modal, MediaService) {
        $scope.media = jsonCache.get($attrs.jsonDataId);

        $scope.tabs = {
            media: {
                active: $attrs.ngType !== 'file',
                disabled: false
            },
            sitemap: {
                active: $attrs.ngType === 'file',
                disabled: $attrs.ngType !== 'file'
            }
        };

        $scope.clearMedia = function() {
            $scope.media = [];
        }

        $scope.isImage = function() {
            if (angular.isUndefined($scope.media.mimeType)) {
                return false;
            }
            return ($scope.media.mimeType.substr(0, 6) == "image/");
        };

        $scope.getSrc = function(media, dimension) {
            return MediaService.getMediaUrl(media.directory, media.filename, media.mimeType, dimension);
        };

        $scope.selectMedia = function() {
            var modalInstance = $modal.open({
                animation: true,
                templateUrl: $attrs.modalTemplate,
                controller: 'MediaModalSelectorController',
                size: 'lg'
            });

            modalInstance.result.then(function(media) {
                if (media !== null) {
                    $scope.media = media;
                }
            }, function () {

            });
        };
}]);

angular.module('media42')
    .controller('MediaModalSelectorController', ['$scope', '$modalInstance', function ($scope, $modalInstance) {
        var selectedMedia = null;


        $scope.selectMedia = function(media) {
            if ($scope.selectedMedia == media.id) {
                $scope.selectedMedia = null;
                selectedMedia = null;

                return;
            }
            $scope.selectedMedia = media.id;
            selectedMedia = media;
        };

        $scope.ok = function () {
            $modalInstance.close(selectedMedia);
        };

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    }]);
;
angular.module('media42')
    .service('MediaService', ['jsonCache', function(jsonCache) {
        this.getMediaUrl = function(directory, filename, mimeType, dimension) {
            if (angular.isUndefined(directory) || angular.isUndefined(filename)) {
                return "";
            }
            var mediaConfig = jsonCache.get("mediaConfig");

            if (mimeType.substr(0, 6) != "image/" || dimension == null) {
                return mediaConfig.baseUrl + directory + filename;
            }

            if (angular.isUndefined(mediaConfig.dimensions[dimension])) {
                return mediaConfig.baseUrl + directory + filename;
            }

            var currentDimension = mediaConfig.dimensions[dimension];

            var extension = filename.split(".").pop();
            var oldFilename = filename;
            filename = filename.substr(0, filename.length - extension.length -1);

            filename = filename + "-" + ((currentDimension.width == "auto") ? "" : currentDimension.width) + "x" + ((currentDimension.height == "auto") ? "" : currentDimension.height) + "." + extension;

            return mediaConfig.baseUrl + directory + filename;
        }
    }]
);