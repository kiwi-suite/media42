angular.module('media42', [
    'angularFileUpload'
]);
;
angular.module('media42')
    .directive('formMedia', [function() {
        return {
            restrict: 'E',
            templateUrl: function(elem, attrs) {
                return attrs.template;
            },
            scope: {
                elementDataId: '@'
            },
            controller: ['$scope', 'jsonCache', '$formService', '$uibModal', 'MediaService', function($scope, jsonCache, $formService, $uibModal, MediaService) {
                $scope.formData = jsonCache.get($scope.elementDataId);

                $scope.onChange = function () {
                    $scope.formData.errors = [];
                };

                $scope.empty = function() {
                    $scope.formData.value = {
                        id: null,
                        directory: null,
                        filename: null,
                        mimeType: null,
                        size: null
                    };
                    $scope.onChange();
                };

                $scope.isImage = function(item) {
                    return MediaService.isImage(item.mimeType);
                };

                $scope.getDocumentClass = function(item) {
                    return MediaService.getDocumentIcon(item.mimeType);
                };

                $scope.getSrc = function(media, dimension) {
                    return MediaService.getMediaUrl(media.directory, media.filename, media.mimeType, dimension);
                };

                $scope.selectMedia = function() {
                    var modalInstance = $uibModal.open({
                        animation: true,
                        templateUrl: 'element/form/media-modal.html',
                        controller: ['$scope', '$uibModalInstance', 'formData', function ($scope, $uibModalInstance, formData) {
                            $scope.selectedMedia = null;
                            $scope.mediaUrl = formData.mediaUrl;

                            $scope.selectMedia = function(media) {
                                if ($scope.selectedMedia !== null && $scope.selectedMedia.id == media.id) {
                                    $scope.selectedMedia = null;

                                    return;
                                }
                                $scope.selectedMedia = media;
                            };

                            $scope.ok = function () {
                                $uibModalInstance.close($scope.selectedMedia);
                            };

                            $scope.cancel = function () {
                                $uibModalInstance.dismiss('cancel');
                            };
                        }],
                        size: 'lg',
                        resolve: {
                            formData: function() {
                                return $scope.formData;
                            }
                        }
                    });

                    modalInstance.result.then(function(media) {
                        if (media !== null) {
                            $scope.formData.value = media;
                        }
                    }, function () {

                    });
                };

                if (angular.isDefined($scope.formData.options.formServiceHash)) {
                    $formService.put(
                        $scope.formData.options.formServiceHash,
                        $scope.formData.name,
                        $scope.elementDataId
                    );
                }
            }]
        }
    }]);
;
angular.module('media42')
    .controller('CropController', ['$scope', '$http', '$timeout', 'Cropper', 'jsonCache', '$attrs', '$interval', function ($scope, $http, $timeout, Cropper, jsonCache, $attrs, $interval) {
        $scope.data = [];

        $scope.dimensions = jsonCache.get($attrs.json)['dimension'];
        $scope.meta = jsonCache.get($attrs.json)['meta'];
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

            $http.post(url, $scope.data[handle]);
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
                    console.log(dataNew);
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
;
angular.module('media42')
    .controller('MediaController', ['$scope', 'FileUploader', '$attrs', '$http', '$sessionStorage', '$templateCache', 'MediaService', '$uibModal', function ($scope, FileUploader, $attrs, $http, $sessionStorage, $templateCache, MediaService, $uibModal) {
        $templateCache.put('template/smart-table/pagination.html',
            '<nav ng-if="numPages && pages.length >= 2"><ul class="pagination">' +
            '<li ng-if="currentPage > 1"><a ng-click="selectPage(1)"><i class="fa fa-angle-double-left"></i></a></li>' +
            '<li ng-if="currentPage > 1"><a ng-click="selectPage(currentPage - 1)"><i class="fa fa-angle-left"></i></a></li>' +
            '<li ng-repeat="page in pages" ng-class="{active: page==currentPage}"><a ng-click="selectPage(page)">{{page}}</a></li>' +
            '<li ng-if="currentPage < numPages"><a ng-click="selectPage(currentPage + 1)"><i class="fa fa-angle-right"></i></a></li>' +
            '<li ng-if="currentPage < numPages"><a ng-click="selectPage(numPages)"><i class="fa fa-angle-double-right"></i></a></li>' +
            '</ul></nav>');

        var currentTableState = {};
        var url = $attrs.url;

        var persistNamespace = null;
        if (angular.isDefined($attrs.persist) && $attrs.persist.length > 0) {
            persistNamespace = $attrs.persist;
        }

        var isInitialCall = true;

        $scope.isCollapsed = true;
        $scope.collection = [];
        $scope.isLoading = true;
        $scope.displayedPages = 1;

        $scope.errorFiles = [];

        $scope.category = $attrs.category;

        var categorySelectElement = angular.element('#media-category-select');

        var uploader = $scope.uploader = new FileUploader({
            url: $attrs.uploadUrl,
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

        $scope.$watch('category',function(newValue, oldValue) {
            if(newValue != oldValue && categorySelectElement.val() != newValue) {
                categorySelectElement.val(newValue).trigger('change');
            }
        },true);

        $scope.delete = function(deleteUrl, id, modalTitle, modalContent) {
            $scope.deleteLoading = true;
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'element/delete-modal.html',
                controller: 'DeleteModalController',
                resolve: {
                    requestUrl: function(){
                        return deleteUrl;
                    },
                    requestParams: function(){
                        return {id: id};
                    },
                    requestTitle: function(){
                        return modalTitle;
                    },
                    requestContent: function(){
                        return modalContent;
                    },
                    requestMethod: function(){
                        return "delete";
                    },
                    requestIcon: function(){
                        return "fa fa-trash-o";
                    }
                }
            });

            modalInstance.result.then(function (data) {
                requestFromServer(url, currentTableState);


                $scope.deleteLoading = false;
            }, function () {
                $scope.deleteLoading = false;
            });
        }

        uploader.onBeforeUploadItem = function onBeforeUploadItem(item) {
            item.formData = [{
                category: $scope.category
            }];
        }


        uploader.onCompleteAll = function() {

            requestFromServer(url, currentTableState);
            $scope.errorFiles = [];
        };

        $scope.isImage = function(item) {
            return MediaService.isImage(item.mimeType);
        };

        $scope.getDocumentClass = function(item) {
            return MediaService.getDocumentIcon(item.mimeType);
        };

        $scope.callServer = function (tableState) {
            currentTableState = tableState;

            if (isInitialCall === true && persistNamespace !== null) {
                if (angular.isDefined($sessionStorage.smartTable) && angular.isDefined($sessionStorage.smartTable[persistNamespace])) {
                    angular.extend(tableState, angular.fromJson($sessionStorage.smartTable[persistNamespace]));
                }
            } else if (persistNamespace !== null) {
                if (angular.isUndefined($sessionStorage.smartTable)) {
                    $sessionStorage.smartTable = {};
                }
                $sessionStorage.smartTable[persistNamespace] = angular.toJson(tableState);
            }

            if (angular.isDefined(tableState['search']) && angular.isDefined(tableState['search']['predicateObject']) && angular.isDefined(tableState['search']['predicateObject']['categorySelection'])) {
                var categorySelection = tableState['search']['predicateObject']['categorySelection'];

                if (categorySelection != '*' && categorySelection != $scope.category) {
                    $scope.category = categorySelection;
                }
            }
            requestFromServer(url, tableState);
        };

        $scope.getSrc = function(media, dimension) {
            return MediaService.getMediaUrl(media.directory, media.filename, media.mimeType, dimension);
        };

        function requestFromServer(url, tableState) {
            $scope.collection = [];
            $scope.isLoading = true;

            isInitialCall = false;
            $http.post(url, tableState).
            success(function(data, status, headers, config) {
                $scope.isLoading = false;

                $scope.collection = data.data;

                $scope.displayedPages = data.meta.displayedPages;
                tableState.pagination.numberOfPages = data.meta.displayedPages;
            }).
            error(function(data, status, headers, config) {
            });
        }
    }]);
;
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
;
angular.module('media42')
    .service('MediaService', ['jsonCache', function(jsonCache) {
            this.getMediaUrl = function(directory, filename, mimeType, dimension) {
                if (angular.isUndefined(directory) || directory == null) {
                    return "";
                }
                if (angular.isUndefined(filename) || filename == null) {
                    return "";
                }
                if (angular.isUndefined(mimeType) || mimeType == null) {
                    return "";
                }

                var mediaConfig = jsonCache.get("mediaConfig");

                directory = directory.replace("data/media", "");

                if (mimeType.substr(0, 6) != "image/" || dimension == null) {
                    return mediaConfig.baseUrl + directory + filename;
                }

                if (angular.isUndefined(mediaConfig.dimensions[dimension])) {
                    return mediaConfig.baseUrl + directory + filename;
                }

                var currentDimension = mediaConfig.dimensions[dimension];

                var extension = filename.split(".").pop();
                filename = filename.substr(0, filename.length - extension.length -1);

                filename = filename + "-" + ((currentDimension.width == "auto") ? "" : currentDimension.width) + "x" + ((currentDimension.height == "auto") ? "" : currentDimension.height) + "." + extension;

                return mediaConfig.baseUrl + directory + filename;
            }

            this.getDocumentIcon = function(mimeType) {
                if (angular.isUndefined(mimeType) || mimeType == null) {
                    return "";
                }

                if (mimeType == "application/pdf") {
                    return "fa-file-pdf-o";
                }

                return "fa-file"
            };

            this.isImage = function(mimeType) {
                if (angular.isUndefined(mimeType) || mimeType == null) {
                    return false;
                }

                return (mimeType.substr(0, 6) == "image/");
            };
        }]
    );
