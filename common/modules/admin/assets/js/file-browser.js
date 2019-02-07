$(function () {
    $("#file-browser-content").on("click", ".file-item", function () {
        var urlEl = $(this).children(".url");
        if (urlEl.hasClass("directory")) {
            getFiles(urlEl.val());
        }
        else {
            var queryStringItems = getQueryStringItems();
            opener.window.parent.CKEDITOR.tools.callFunction(queryStringItems.CKEditorFuncNum, urlEl.val());
            window.close();
        }
        return false;
    });

    $("#breadcrumb").on("click", "a", function () {
        getFiles($(this).attr("href"));
        return false;
    });

    function getFiles(directory) {
        var queryStringItems = getQueryStringItems();
        var data = {};
        data[yii.getCsrfParam()] = yii.getCsrfToken();
        data["directory"] = directory;
        $.ajax({
            url: "/admin/file-browser/browser-items?site_id=" + queryStringItems.site_id,
            type: "POST",
            data: data
        }).done(function (html) {
            $("#file-browser-content").html(html);
            $("#directory").val(directory);
            setBreadcrumb();
        }).fail(function (jqXHR, textStatus) {
            alert(jqXHR.responseText);
        });
    }


    function setBreadcrumb() {
        var breadcrumb = $("#breadcrumb");
        var directory = $("#directory").val();
        var uploadDir = $("#upload-dir").val();
        if (directory == uploadDir) {
            breadcrumb.addClass("hidden");
        }
        else {
            var breadcrumbItems = [];
            var uploadDirItems = uploadDir.split('/');
            var dirName = "";
            var item = null;
            for (var i = 0, length = uploadDirItems.length; i < length; i++) {
                item = uploadDirItems[i];
                if (item) {
                    dirName = item;
                }
            }
            breadcrumbItems.push({directory: uploadDir, name: dirName});
            var directoryItems = directory.substring(uploadDir.length).split('/');
            for (var i = 0, length = directoryItems.length; i < length; i++) {
                item = directoryItems[i];
                if (item) {
                    breadcrumbItems.push({directory: uploadDir + "/" + item + "/", name: item});
                }
            }
            var content = "";
            var breadcrumbItemsLength = breadcrumbItems.length;
            for (var i = 0; i < breadcrumbItemsLength - 1; i++) {
                item = breadcrumbItems[i];
                content = content + '<li><a href="' + item.directory + '">' + item.name + '</a></li>';
            }
            item = breadcrumbItems[breadcrumbItemsLength - 1];
            content = content + '<li class="active">' + item.name + '</li>';
            breadcrumb.html(content);
            breadcrumb.removeClass("hidden");
        }
    }

    $("#file-upload").on("click", function () {
        $("#file").click();
        return false;
    });

    $("#create-folder").on("click", function () {
        var createFolderBtn = $(this);
        var createFolderForm = $("#create-folder-form");
        var nameEl = createFolderForm.find("[name='name']");
        var isValid = checkEmpty(nameEl);
        if (isValid) {
            createFolderBtn.progressButton();
            var queryStringItems = getQueryStringItems();
            var data = {};
            data[yii.getCsrfParam()] = yii.getCsrfToken();
            data["name"] = nameEl.val();
            data["directory"] = $("#directory").val();
            $.ajax({
                url: "/admin/file-browser/create-directory?site_id=" + queryStringItems.site_id,
                type: "POST",
                data: data
            }).done(function (html) {
                $("#file-browser-content").html(html);
                createFolderForm.modal("hide");
                $("#directory").val($("#directory").val() + nameEl.val() + "/");
                setBreadcrumb();
                createFolderBtn.progressButton("stop");
            }).fail(function (jqXHR, textStatus) {
                createFolderBtn.progressButton("stop");
                alert(jqXHR.responseText);
            });
        }
        return false;
    });

    function checkEmpty(el) {
        if (el.val().length == 0) {
            el.parent().addClass("has-error");
            return false;
        } else {
            el.parent().removeClass("has-error");
            return true;
        }
    }

    var file = $("#file");
    file.on("change", function (event) {
        var queryStringItems = getQueryStringItems();
        var formData = new FormData();
        formData.append(yii.getCsrfParam(), yii.getCsrfToken());
        formData.append("directory", $("#directory").val());
        formData.append("upload", file[0].files[0]);
        $.ajax({
            url: "/admin/file-browser/upload-file?site_id=" + queryStringItems.site_id,
            type: "POST",
            contentType: false,
            processData: false,
            data: formData
        }).done(function (url) {
            opener.window.parent.CKEDITOR.tools.callFunction(queryStringItems.CKEditorFuncNum, url);
            window.close();
        }).fail(function (jqXHR, textStatus) {
            alert(jqXHR.responseText);
        });
        return false;
    });

    function getQueryStringItems() {
        var queryStringItems = {}, nameValue = null;
        var nameValueItems = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < nameValueItems.length; i++) {
            nameValue = nameValueItems[i].split('=');
            queryStringItems[nameValue[0]] = nameValue[1];
        }
        return queryStringItems;
    }
});


