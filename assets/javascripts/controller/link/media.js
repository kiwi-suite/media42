angular.module('media42')
    .controller('LinkMediaController',['$scope', function($scope){
        $scope.selectedMedia = null;
        var inititalValue = $scope.link.getValue();
        if (inititalValue !== null) {
            $scope.selectedMedia = inititalValue;
        }

        $scope.selectMedia = function(media) {
            $scope.link.setValue(media);
            $scope.selectedMedia = media;
        };
    }]
);
