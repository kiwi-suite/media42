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
            controller: ['$scope', 'jsonCache', '$formService', '$uibModal', function($scope, jsonCache, $formService, $uibModal) {
                $scope.formData = jsonCache.get($scope.elementDataId);

                $scope.onChange = function () {
                    $scope.formData.errors = [];
                };

                $scope.empty = function() {
                    $scope.formData.value = "";
                    $scope.onChange();
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
                            $scope.media = media;
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
