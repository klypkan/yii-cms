var Admin = Admin || {};
$(function () {
    var saveBtn = $("#save-btn"),
        title = $("#title"),
        content = $("#content"),
        permalink_name = $("#permalink_name"),
        site_id = $("#site_id");
    if (saveBtn.length > 0) {
        saveBtn.parents("form:first").on("beforeSubmit", function () {
            saveBtn.progressButton();
        });
    }

    $("#cancel-btn").on("click", function () {
        location.assign($(this).attr("data-return-url"));
    });

    if (content.length > 0) {
        CKEDITOR.replace("content", {
            filebrowserBrowseUrl: "/admin/file-browser/browser?type=file&site_id=" + site_id.val(),
            filebrowserImageBrowseUrl: "/admin/file-browser/browser?type=image&site_id=" + site_id.val(),
            extraAllowedContent: "video[width,height,controls,src,preload,poster](*);source[src,type]"
        });
    }

    if (title.length > 0
        && permalink_name.length > 0
        && site_id.length > 0) {
        title.on("change", function () {
            if (permalink_name.val().length == 0
                && title.val().length > 0) {
                permalink_name.prop("disabled", true);
                var data = {};
                data[yii.getCsrfParam()] = yii.getCsrfToken();
                data["site_id"] = site_id.val();
                data["name"] = title.val();

                $.ajax({
                    url: "/admin/default/create-slug",
                    type: "POST",
                    data: data
                }).done(function (data) {
                    permalink_name.val(data);
                    permalink_name.prop("disabled", false);
                }).fail(function (jqXHR, textStatus) {
                    permalink_name.prop("disabled", false);
                });
            }
        });
    }
});



