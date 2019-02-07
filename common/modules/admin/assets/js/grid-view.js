$(function () {
    $("#add-entity").on("click", function () {
        edit($(this).attr("data-edit-url"));
    });

    $("#delete-entity").on("click", function () {
        hideError();
        var entityListTable = $("#entity-list-table");
        var idItems = [];
        entityListTable.find(".entity-selector:checked").each(function (index) {
            idItems.push($(this).parents("tr:first").attr("data-key"));
        });

        if (idItems.length > 0) {
            var btn = $(this);
            btn.progressButton();

            var data = {};
            data[yii.getCsrfParam()] = yii.getCsrfToken();
            data["idItems"] = idItems;

            $.ajax({
                url: btn.attr("data-delete-url"),
                type: "POST",
                data: data
            }).done(function (data) {
                location.reload(true);
            }).fail(function (jqXHR, textStatus) {
                showError(jqXHR.responseText);
                btn.progressButton("stop");
            });
        }
    });

    $("#filter-entity-list").on("click", function () {
        if ($(this).parent().hasClass("active")) {
            close();
        }
        else {
            var el = $(this).parent();
            var filterContainer = $("#entity-list-filter");
            el.addClass("active");
            filterContainer.removeClass("hidden");
            filterContainer.find("input[name='show-filter']").val(true);
        }
    });

    $("#apply-filter-btn").on("click", function () {
        var filterContainer = $("#entity-list-filter"), filter = [], filterItem = null, valEl = null, operator = null;
        filterContainer.find(".filter-item").each(function (index) {
            filterItem = $(this);
            operator = filterItem.find(".filter-item-operator-selector").val();
            if (operator != "NoSet") {
                valEl = filterItem.find("[data-name]");
                filter.push({name: valEl.attr("data-name"), opr: operator, val: valEl.val()});
            }
        });
        filterContainer.find("input[name='filter']").val(JSON.stringify(filter));
        filterContainer.find("form:first").submit();
    });

    $("#clear-filter-btn").on("click", function () {
        $("#entity-list-filter").find(".filter-item").each(function (index) {
            $(this).find("select").val("NoSet");
        });
    });

    $("#close-filter-btn").on("click", function () {
        close();
    });

    $(".entity-list-item-editable td").on("click", function () {
        var el = $(this);
        var queryStringItems = getQueryStringItems();
        if (queryStringItems.mode == "select") {
            opener[queryStringItems.sender](el.parents("tr:first").attr("data-key"));
            window.close();
        }
        else {
            if (!el.hasClass("entity-list-item-selector")) {
                edit(el.parents("tr:first").attr("data-edit-url"));
            }
        }
    });

    $("#select-entity-list").on("change", function () {
        var el = $(this);
        var checked = el.prop("checked");
        var table = el.parents("table:first");
        table.find(".entity-selector").prop("checked", checked);
    });

    $('[data-toggle="tooltip"]').tooltip();

    function edit(url) {
        location.assign(url);
    }

    function close() {
        var el = $("#filter-entity-list").parent();
        var filterContainer = $("#entity-list-filter");
        el.removeClass("active");
        filterContainer.addClass("hidden");
        filterContainer.find("input[name='show-filter']").val(false);
        filterContainer.find("form:first").submit();
    }

    function showError(error) {
        var entityListError = $("#entity-list-error");
        entityListError.html(error);
        entityListError.removeClass("hidden");
    }

    function hideError() {
        $("#entity-list-error").addClass("hidden");
    }

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


