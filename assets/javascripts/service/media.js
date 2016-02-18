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