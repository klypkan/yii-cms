$(function () {
    var appTable = $("#app-table");

    getNewVersionsOfApps();

    $("#app-list-selector").on("change", function () {
        var el = $(this);
        var checked = el.prop("checked");
        appTable.find(".app-selector").each(function (index) {
            var itemEl = $(this);
            if (!itemEl.prop("disabled")) {
                itemEl.prop("checked", checked);
            }
        });
    });

    $("#update-btn").on("click", function () {
        var updateBtn = $(this);
        updateBtn.progressButton();
        var apps = [];
        appTable.find(".app-selector:checked").each(function (index) {
            var trEl = $(this).parents("tr:first");
            apps.push({name: trEl.attr("data-app-name"), version: trEl.attr("data-app-version")});
        });

        if (apps.length > 0) {
            var data = {};
            data[yii.getCsrfParam()] = yii.getCsrfToken();
            data["apps"] = apps;
            $.ajax({
                url: "/admin/update/update",
                type: "POST",
                data: data
            }).done(function (data) {
                var item = null, trEl = null, appSelectorEl = null;
                for (var i = 0, length = data.length; i < length; i++) {
                    item = data[i];
                    trEl = appTable.find("tr[data-app-name='" + item.name + "']");
                    trEl.find(".app-installed-version").text(item.version);
                    appSelectorEl = trEl.find(".app-selector");
                    if (trEl.find(".app-new-version").text() > item.version) {
                        appSelectorEl.prop("disabled", false);
                        appSelectorEl.prop("checked", true);
                    }
                    else {
                        appSelectorEl.prop("checked", false);
                        appSelectorEl.prop("disabled", true);
                    }
                }
                updateBtn.progressButton("stop");
            }).fail(function (jqXHR, textStatus) {

            });
        }
        else {
            updateBtn.progressButton("stop");
        }
    });

    function getNewVersionsOfApps() {
        var apps = [];
        appTable.find(".app-selector").each(function (index) {
            var trEl = $(this).parents("tr:first");
            apps.push({name: trEl.attr("data-app-name"), version: trEl.attr("data-app-version")});
        });

        if (apps.length > 0) {
            var data = {};
            data[yii.getCsrfParam()] = yii.getCsrfToken();
            data["apps"] = apps;
            $.ajax({
                url: "/admin/update/get-new-versions-of-apps",
                type: "POST",
                data: data
            }).done(function (data) {
                var item = null, trEl = null, appSelectorEl = null;
                for (var i = 0, length = data.length; i < length; i++) {
                    item = data[i];
                    trEl = appTable.find("tr[data-app-name='" + item.name + "']");
                    trEl.find(".app-new-version").text(item.version);
                    appSelectorEl = trEl.find(".app-selector");
                    if (item.version > trEl.find(".app-installed-version").text()) {
                        appSelectorEl.prop("disabled", false);
                        appSelectorEl.prop("checked", true);
                    }
                    else {
                        appSelectorEl.prop("checked", false);
                        appSelectorEl.prop("disabled", true);
                    }
                }
            }).fail(function (jqXHR, textStatus) {

            });
        }
    }
});


