angular.module('admin42')
    .controller('MediaController', ['$scope', 'FileUploader', '$attrs', '$http', '$sessionStorage', '$templateCache', 'toaster', 'MediaService', function ($scope, FileUploader, $attrs, $http, $sessionStorage, $templateCache, toaster, MediaService) {
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
        var isInitialCall = true;

        $scope.isCollapsed = true;
        $scope.collection = [];
        $scope.isLoading = true;
        $scope.displayedPages = 1;

        $scope.errorFiles = [];

        $scope.category = $attrs.category;



        if (angular.isDefined($attrs.persist) && $attrs.persist.length > 0) {
            persistNamespace = $attrs.persist;
        }

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

        $scope.uploadCategoryChange = function() {
            $('#categorySearchSelect').val($scope.category);
            angular.element($('#categorySearchSelect')[0]).triggerHandler('input');
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
            return (item.mimeType.substr(0, 6) == "image/");
        };

        $scope.getDocumentClass = function(item) {
            return "fa-file";
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
