$(function () {
    var site_id = $("#site_id"), file = $("#file"), postTagList = $("#post-tag-list"), maxPostTagIndex = postTagList.children("li").length;

    $("#file-upload-btn").on("click", function () {
        file.click();
        return false;
    });

    $("#file-remove-btn").on("click", function () {
        var fileParent = $(this).parents(".form-group:first");
        fileParent.find(".img-editor-image").attr("src", "");
        fileParent.find(".img-editor-value").val("");
        return false;
    });

    file.on("change", function (event) {
        var formData = new FormData();
        var directory = file.attr("data-directory");
        if (!directory) {
            directory = "";
        }
        formData.append(yii.getCsrfParam(), yii.getCsrfToken());
        formData.append("directory", directory);
        formData.append("upload", file[0].files[0]);

        $.ajax({
            url: "/admin/file-browser/upload-file?site_id=" + site_id.val(),
            type: "POST",
            contentType: false,
            processData: false,
            data: formData
        }).done(function (url) {
            var fileParent = file.parent();
            fileParent.find(".img-editor-image").attr("src", url);
            fileParent.find(".img-editor-value").val(url);
        }).fail(function (jqXHR, textStatus) {
            alert(jqXHR.responseText);
        });

        return false;
    });

    $("#add-tags-btn").on("click", function () {
        var addTagsTextEl = $("#add-tags-text");
        var addTagsText = $("#add-tags-text").val();
        if (addTagsText) {
            var addBtn = $(this);
            addBtn.progressButton();

            var tags = addTagsText.split(",");
            var newTags = [];
            var existTags = [];
            postTagList.find(".post-tag-name").each(function (index) {
                existTags.push($(this).val());
            });

            var item = null;
            for (var i = 0, length = tags.length; i < length; i++) {
                item = tags[i].trim();
                if (existTags.indexOf(item) < 0) {
                    newTags.push(item);
                }
            }

            if (newTags.length > 0) {
                var data = {};
                data[yii.getCsrfParam()] = yii.getCsrfToken();
                data["site_id"] = site_id.val();
                data["tags"] = newTags;

                $.ajax({
                    url:  "/admin/post/get-tags?site_id=" + site_id.val(),
                    type: "POST",
                    data: data
                }).done(function (data) {
                    var content = '';
                    var item = null;
                    for (var i = 0, length = data.length; i < length; i++) {
                        item = data[i];
                        content = content + '<li>';
                        content = content + '<input type="hidden" name="PostForm[tags][' + maxPostTagIndex + i + '][id]" value="' + item.id + '" />';
                        content = content + '<input type="hidden" class="post-tag-name" name="PostForm[tags][' + maxPostTagIndex + i + '][name]" value="' + item.name + '" />';
                        content = content + '<i class="fa fa-times-circle fa-lg post-tag-remove"></i> ' + item.name;
                        content = content + '</li>';
                    }
                    maxPostTagIndex = maxPostTagIndex + data.length;
                    postTagList.append(content);
                    addTagsTextEl.val('');

                    addBtn.progressButton("stop");
                });
            }
        }
    });

    postTagList.on("click", ".post-tag-remove", function () {
        $(this).parent().remove();
    });
});



