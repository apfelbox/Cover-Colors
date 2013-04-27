/*global jQuery:true */
(function ($)
{
    var Upload = {
        $noCover: null,
        $coverPreview: null,
        $metrics: null,
        $trackListing: null,
        hasActiveRequest: false,

        init: function ()
        {
            this.$noCover = $(".cover .no-cover");
            this.$coverPreview = $(".cover img");
            this.$metrics = $(".metrics");
            this.$trackListing = $(".track-listing");

            if (!this.dragAndDropIsSupported())
            {
                alert("This demo will not work, since the File APIs are not fully supported in this browser.");
                return;
            }

            this._registerEventListeners();
        },


        _registerEventListeners: function ()
        {
            $("body")
                .on("dragover", $.proxy(this._onDragOver, this))
                .on("dragleave", $.proxy(this._onDragOut, this))
                .on("drop", $.proxy(this._onDrop, this));
        },


        /**
         * Event listener for drag over event
         *
         * @private
         */
        _onDragOver: function (event)
        {
            $("body").addClass("drag-hover");

            event.preventDefault();
            event.stopPropagation();
        },


        /**
         * Event listener for drag out event
         *
         * @private
         */
        _onDragOut: function (event)
        {
            $("body").removeClass("drag-hover");

            event.preventDefault();
            event.stopPropagation();
        },


        /**
         * Event listener for drop event
         * @private
         */
        _onDrop: function (event)
        {
            this._onDragOut(event);
            var files = event.originalEvent.dataTransfer.files;

            // error: more than one file dropped
            if (files.length != 1)
            {
                alert("Please upload (only) one file.");
                return;
            }

            var file = files[0];

            // error: file too big
            if (file.size > 1024 * 1024)
            {
                alert("Max file size is 1MB.");
                return;
            }

            // error: not an image
            if (!file.type.match(/^image\/.*$/))
            {
                alert("Only images allowed.");
                return;
            }

            var reader = new FileReader();
            reader.onload = $.proxy(this._onFileRead, this);
            reader.readAsDataURL(file);
        },


        /**
         * Callback, when the file has finished loading
         *
         * @param fileData
         * @private
         */
        _onFileRead: function (fileData)
        {
            if (this.hasActiveRequest)
            {
                return;
            }

            this.hasActiveRequest = true;
            var imageContent = fileData.target.result;
            this._showUploadInfo();

            var req = $.ajax({
                url: "analyze.php",
                data: {file: imageContent},
                type: "POST",
                dataType: "json"
            });

            req.fail($.proxy(this._onUploadFail, this));


            var self = this;
            req.done(
                function (data)
                {
                    self._onUploadResponse(data, imageContent);
                }
            );
        },


        /**
         * Callback on failed image upload
         * @private
         */
        _onUploadFail: function ()
        {
            this.hasActiveRequest = false;
            alert("Upload failed. Please reload and try again.");
        },


        /**
         *
         * @param data
         * @param imageContent
         * @private
         */
        _onUploadResponse: function (data, imageContent)
        {
            this.hasActiveRequest = false;
            this._setColors(data);
            this._setMetricsData(data);
            this._showImagePreview(imageContent);
        },


        /**
         * Sets the colors
         *
         * @param data
         * @private
         */
        _setColors: function (data)
        {
            $("body").css("background-color", data.result.background);
            this.$trackListing.find("h2").css("color", data.result.title);
            this.$trackListing.find("p").css("color", data.result.songs);
        },


        /**
         * Displays the metrics data
         * @param data
         * @private
         */
        _setMetricsData: function (data)
        {
            for (var type in data.metrics)
            {
                this.$metrics.find("." + type).text(data.metrics[type]);
            }

            this.$metrics.show();
        },


        /**
         *
         * @param imageContent
         * @private
         */
        _showImagePreview: function (imageContent)
        {
            this.$noCover.hide();
            this.$coverPreview.prop("src", imageContent).show();
        },


        /**
         * Displays the uploading message
         * @private
         */
        _showUploadInfo: function ()
        {
            this.$noCover.text("Uploading & Analyzing...").show();
            this.$coverPreview.hide();
        },


        /**
         * Returns whether the file drag&drop Api is supported
         * @returns {*}
         */
        dragAndDropIsSupported: function ()
        {
            return window.File && window.FileList && window.FileReader && window.Blob;
        }
    };



    $(
        function ()
        {
            Upload.init();
        }
    );

})(jQuery);