$(function () {
    var site_id = $("#site_id"), postTagList = $("#post-tag-list"), maxPostTagIndex = postTagList.children("li").length;

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



