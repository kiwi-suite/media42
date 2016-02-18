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
