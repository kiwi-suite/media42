angular.module('media42')
    .controller('CropController', ['$scope', '$http', '$timeout', 'Cropper', 'jsonCache', '$attrs', '$interval', function ($scope, $http, $timeout, Cropper, jsonCache, $attrs, $interval) {
        $scope.data = [];

        $scope.dimensions = jsonCache.get($attrs.json)['dimension'];
        $scope.meta = jsonCache.get($attrs.json)['meta']['crop'];
        $scope.selectedHandle = null;

        var imageSize = jsonCache.get($attrs.json)['imageSize'];

        $scope.hasChanges = {};

        $scope.currentInfo = {
            x: 0,
            y: 0,
            width: 0,
            height: 0,
            rotate: 0,
            calcWidth: 0,
            calcHeight: 0
        };

        $scope.isActive = function(handle) {
            if (handle == $scope.selectedHandle) {
                return 'active';
            }

            return '';
        };

        $scope.checkChanges = function(handle) {
            if (angular.isUndefined($scope.data[handle])) {
                return false;
            }
            if (angular.isUndefined($scope.meta[handle])) {
                return true;
            }
            if ($scope.data[handle].x == $scope.meta[handle].x &&
                $scope.data[handle].y == $scope.meta[handle].y &&
                $scope.data[handle].width == $scope.meta[handle].width &&
                $scope.data[handle].height == $scope.meta[handle].height
            ) {
                return false;
            }

            return true;
        };

        $scope.checkImageSize = function(currentDimension){
            if (imageSize.width < currentDimension.width || imageSize.height < currentDimension.height) {
                return false;
            }

            return true;
        };
        angular.forEach($scope.dimensions, function(value, key) {
            if (this.selectedHandle !== null) {
                return;
            }

            if (!this.checkImageSize(value)) {
                return;
            }

            this.selectedHandle = key;
        }, $scope);

        $scope.saveCroppedImage = function(handle, url) {
            if (angular.isUndefined($scope.data[handle])) {
                return false;
            }

            url = url.replace('{{ name }}', handle);

            $http.post(url, $scope.data[handle]).success(function(){
                $scope.hasChanges[handle] = false;
            });
        };

        function setCurrentInfo(currentInfo) {
            var dimension = $scope.dimensions[$scope.selectedHandle];

            if (dimension.width != "auto" && dimension.height != "auto") {
                currentInfo.calcWidth = dimension.width;
                currentInfo.calcHeight = dimension.height;
            } else if(dimension.width == "auto" && dimension.height == "auto") {
                currentInfo.calcWidth = currentInfo.width;
                currentInfo.calcHeight = currentInfo.height;
            } else if (dimension.width == "auto" && dimension.height != "auto") {
                var ratio = currentInfo.height/dimension.height;

                currentInfo.calcWidth = Math.round(currentInfo.width/ratio);
                currentInfo.calcHeight = dimension.height;
            } else {
                var ratio = currentInfo.width/dimension.width;

                currentInfo.calcWidth = dimension.width;
                currentInfo.calcHeight = Math.round(currentInfo.height / ratio);
            }
            $scope.currentInfo = currentInfo;
            $scope.$apply();
        }

        $scope.selectDimension = function(handle) {
            $scope.currentInfo = {
                x: 0,
                y: 0,
                width: 0,
                height: 0,
                rotate: 0,
                calcWidth: 0,
                calcHeight: 0
            };
            var dimension = $scope.dimensions[handle];

            Cropper.getJqueryCrop().cropper("destroy");

            $scope.selectedHandle = handle;

            var options = {
                crop: function(dataNew) {
                    $scope.data[$scope.selectedHandle] = {
                        'x': dataNew.x,
                        'y': dataNew.y,
                        'width': dataNew.width,
                        'height': dataNew.height
                    };
                },
                viewMode: 1,
                strict: true,
                zoomable: false,
                responsive: true,
                rotatable: false,
                guides: true
            };

            if (!angular.isUndefined($scope.data[handle])) {
                options.data = $scope.data[handle];
            } else if (!angular.isUndefined($scope.meta[handle])) {
                options.data = {"x": $scope.meta[handle].x, "y": $scope.meta[handle].y, "width":$scope.meta[handle].width, "height":$scope.meta[handle].height, "rotate":0};
            } else {
                options.data = { "width": dimension.width, "height": dimension.height,  "rotate":0};
                options.built = function(e) {
                    var data = $(this).cropper('getData');
                    var imageData = $(this).cropper('getImageData');

                    var x = (imageData.naturalWidth - data.width) / 2;
                    var y = (imageData.naturalHeight - data.height) / 2;

                    $(this).cropper("setData", {"x": x, "y": y});
                }
            }

            if (dimension.width != 'auto' && dimension.height != 'auto') {
                options.aspectRatio = dimension.width / dimension.height;
            }

            Cropper.getJqueryCrop().off('cropmove.cropper');
            Cropper.getJqueryCrop().off('cropstart.cropper');

            Cropper.getJqueryCrop().cropper(options);

            Cropper.getJqueryCrop().on('cropmove.cropper', function (e) {
                var $cropper = $(e.target);

                var data = $cropper.cropper('getCropBoxData');
                var imageData = $cropper.cropper('getImageData');

                setCurrentInfo($cropper.cropper('getData', true));

                if (dimension.width != 'auto' && data.width < dimension.width / (imageData.naturalWidth/imageData.width)) {
                    return false;
                }

                if (dimension.height != 'auto' && data.height < dimension. height / (imageData.naturalHeight/imageData.height)) {
                    return false;
                }

                return true;
            }).on('cropstart.cropper', function (e) {
                var $cropper = $(e.target);

                var data = $cropper.cropper('getCropBoxData');
                var imageData = $cropper.cropper('getImageData');
                var hasChanged = false;

                setCurrentInfo($cropper.cropper('getData', true));

                if (dimension.width != 'auto') {
                    var width = dimension.width / (imageData.naturalWidth/imageData.width);
                    if (angular.isUndefined(data.width) || data.width < width) {
                        data.width = width;
                        hasChanged = true;
                    }
                }

                if (dimension.height != 'auto') {
                    var height = dimension.height / (imageData.naturalHeight/imageData.height);
                    if (angular.isUndefined(data.height) || data.height < height) {
                        data.height = height;
                        hasChanged = true;
                    }

                }

                $scope.hasChanges[handle] = true;
                $scope.$apply();

                if (hasChanged) {
                    $(e.target).cropper('setCropBoxData', data);
                }
            }).on('built.cropper', function (e) {
                var $cropper = $(e.target);
                setCurrentInfo($cropper.cropper('getData', true));
            });
        };

        var stop = $interval(function() {
            if (Cropper.getJqueryCrop() != null && $scope.selectedHandle != null) {
                $scope.selectDimension($scope.selectedHandle);
                stopInterval();
            }
        }, 100);
        function stopInterval() {
            $interval.cancel(stop);
        }
    }]);

angular.module('media42')
    .directive('ngCropper', ['Cropper', function(Cropper) {
        return {
            restrict: 'A',
            link: function(scope, element, atts) {
                Cropper.setJqueryCrop(element);
            }
        };
    }])
.service('Cropper', [function() {
    this.crop = null;

    this.setJqueryCrop = function(crop) {
        this.crop = crop;
    };
    this.getJqueryCrop = function() {
        return this.crop;
    };
}]);
