$(function () {
    var file = $("#file"), fileUploadBtn = $("#file-upload-btn"), loadBtn = $("#load-btn"), fileName = "";

    $("input[name='item_types']").on("change", function () {
        var item_types = [];
        $("input[name='item_types']:checked").each(function(index) {
            item_types.push($(this).val());
        });
        loadBtn.prop("disabled", item_types.length == 0);
    });

    loadBtn.on("click", function () {
        loadBtn.progressButton();
        var item_types = [];
        $("input[name='item_types']:checked").each(function(index) {
            item_types.push($(this).val());
        });
        var data = {};
        data[yii.getCsrfParam()] = yii.getCsrfToken();
        data["file_name"] = fileName;
        data["item_types"] = item_types.join();

        var queryStringItems = getQueryStringItems();
        $.ajax({
            url: "/admin/import/wordpress-load?site_id=" + queryStringItems.site_id,
            type: "POST",
            data: data
        }).done(function (data) {
            $("#post-loaded-number").text(data.postLoaded);
            $("#page-loaded-number").text(data.pageLoaded);

            $("#import-wordpress-step2").addClass("hidden");
            $("#import-wordpress-step3").removeClass("hidden");

            loadBtn.progressButton("stop");
        }).fail(function (jqXHR, textStatus) {
            alert(jqXHR.responseText);
        });
    });

    fileUploadBtn.on("click", function () {
        file.click();
        return false;
    });

    $("#renew-btn").on("click", function () {
        $("#import-wordpress-step3").addClass("hidden");
        $("#import-wordpress-step1").removeClass("hidden");
        return false;
    });

    file.on("change", function (event) {
        fileUploadBtn.progressButton();
        var queryStringItems = getQueryStringItems();
        var formData = new FormData();
        formData.append(yii.getCsrfParam(), yii.getCsrfToken());
        formData.append("upload", file[0].files[0]);
        $.ajax({
            url: "/admin/import/upload-file?site_id=" + queryStringItems.site_id,
            type: "POST",
            contentType: false,
            processData: false,
            data: formData
        }).done(function (data) {
            fileName = data;
            $("#import-wordpress-step1").addClass("hidden");
            $("#import-wordpress-step2").removeClass("hidden");
            fileUploadBtn.progressButton("stop");
        }).fail(function (jqXHR, textStatus) {
            fileUploadBtn.progressButton("stop");
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


