angular.module('media42')
    .controller('ReplaceUploaderController', ['$scope', 'FileUploader', '$attrs', '$window', function ($scope, FileUploader, $attrs, $window) {
        $scope.errorFiles = [];

        $scope.uploader = new FileUploader({
            url: $attrs.uploadUrl,
            autoUpload: true,
            queueLimit: 1,
            removeAfterUpload: true,
            filters: [{
                name: 'filesize',
                fn: function(item) {
                    if (item.size > $attrs.maxFileSize) {
                        $scope.errorFiles.push(item);

                        return false;
                    }

                    return true;
                }
            }]
        });

        $scope.uploader.onSuccessItem = function() {
            $window.location.reload();
        };
    }]);
