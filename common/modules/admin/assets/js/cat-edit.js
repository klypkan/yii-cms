var Admin = Admin || {};
$(function () {
    var name = $("#name"), value = $("#value"), site_id = $("#site_id");

    name.on("change", function () {
        if (name.val().length > 0
            && value.val().length == 0) {
            value.prop("disabled", true);
            var data = {};
            data[yii.getCsrfParam()] = yii.getCsrfToken();
            data["site_id"] = site_id.val();
            data["name"] = name.val();

            $.ajax({
                url: "/admin/category-admin/create-slug?site_id="+site_id.val(),
                type: "POST",
                data: data
            }).done(function (data) {
                value.val(data);
                value.prop("disabled", false);
            }).fail(function (jqXHR, textStatus) {
                value.prop("disabled", false);
            });
        }
    });
});



