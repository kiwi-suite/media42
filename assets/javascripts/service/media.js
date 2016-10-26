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

                var baseUrl = "";
                if (mediaConfig.baseUrl !== null) {
                    baseUrl = mediaConfig.baseUrl;
                }

                if (mimeType.substr(0, 6) != "image/" || dimension == null) {
                    return baseUrl + directory + filename;
                }

                if (angular.isUndefined(mediaConfig.dimensions[dimension])) {
                    return baseUrl + directory + filename;
                }

                var currentDimension = mediaConfig.dimensions[dimension];

                var extension = filename.split(".").pop();
                filename = filename.substr(0, filename.length - extension.length -1);

                filename = filename + "-" + ((currentDimension.width == "auto") ? "" : currentDimension.width) + "x" + ((currentDimension.height == "auto") ? "" : currentDimension.height) + "." + extension;

                return baseUrl + directory + filename;
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
