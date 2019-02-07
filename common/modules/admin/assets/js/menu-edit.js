$(function () {
    var site_id = $("#site_id"),
        menuItemTypeSelector = $("#menu-item-type-selector"),
        menuItemEntitySearch = $("#menu-item-entity-search"),
        menuItemEntityList = $("#menu-item-entity-list"),
        menuItemList = $("#menu-item-list"),
        addMenuItemBtn = $("#add-menu-item-btn"),
        maxDepth = 5;

    getEntityList();

    addMenuItemBtn.on("click", function () {
        addMenuItemBtn.progressButton();
        var menuItemType = menuItemTypeSelector.val();
        var menuItems = [];
        if (menuItemType < 4) {
            $(".menu-item-entity-list").find("input:checked").each(function (index) {
                var menuItem = {};
                menuItem.name = $(this).attr("data-menu-item-entity-name");
                menuItem.type = menuItemTypeSelector.val();
                menuItem.type_name = $("#menu-item-type-selector option:selected").text();
                menuItem.value = $(this).attr("data-menu-item-entity-id");
                menuItems.push(menuItem);

            });
            if (menuItems.length > 0) {
                addMenuItems(menuItems);
            }
            else {
                addMenuItemBtn.progressButton("stop");
            }
        }
        else {
            if (validateNewMenuItem(menuItemType)) {
                var menuItem = {};
                menuItem.name = $("#menu-item-name").val();
                menuItem.type = menuItemTypeSelector.val();
                menuItem.type_name = $("#menu-item-type-selector option:selected").text();
                menuItem.value = $("#menu-item-url").val();
                menuItems.push(menuItem);
                addMenuItems(menuItems);
            }
            else {
                addMenuItemBtn.progressButton("stop");
            }
        }
    });

    menuItemList.on("shown.bs.collapse", ".collapse", function () {
        var liEl = $(this).parents("li:first");
        liEl.removeClass("menu-item-state-hidden");
        liEl.addClass("menu-item-state-shown");

        setMoveMenu(liEl);
    });

    menuItemList.on("hidden.bs.collapse", ".collapse", function () {
        var liEl = $(this).parents("li:first");
        liEl.removeClass("menu-item-state-shown");
        liEl.addClass("menu-item-state-hidden");
    });

    menuItemList.on("click", ".menu-item-remove-btn", function () {
        $(this).parents("li:first").remove();
    });

    menuItemList.on("click", ".menu-item-up-btn", function () {
        var liEl = $(this).parents("li:first");
        var prevEl = liEl.prev("li");
        if (prevEl.length > 0 && prevEl.attr("data-depth") == liEl.attr("data-depth")) {
            prevEl.before(liEl);
            setAllShownMoveMenu();
        }
    });

    menuItemList.on("click", ".menu-item-down-btn", function () {
        var liEl = $(this).parents("li:first");
        var nextEl = liEl.next("li");
        if (nextEl.length > 0 && nextEl.attr("data-depth") == liEl.attr("data-depth")) {
            nextEl.after(liEl);
            setAllShownMoveMenu();
        }
    });

    menuItemList.on("click", ".menu-item-under-btn", function () {
        var liEl = $(this).parents("li:first");
        var prevEl = liEl.prev("li");
        if (prevEl.length > 0) {
            var liElDepth = liEl.attr("data-depth");
            var prevElDepth = prevEl.attr("data-depth");
            if (liElDepth == prevElDepth && liElDepth < maxDepth) {
                var prevId = prevEl.children('input[data-name="id"]').val();
                liEl.children('input[data-name="parent_id"]').val(prevId);
                liEl.removeClass("menu-item-depth-" + liElDepth);
                liElDepth++;
                liEl.attr("data-depth", liElDepth);
                liEl.addClass("menu-item-depth-" + liElDepth);
            }
            setAllShownMoveMenu();
        }
    });

    menuItemList.on("click", ".menu-item-out-from-under-btn", function () {
        var liEl = $(this).parents("li:first");
        var liElDepth = liEl.attr("data-depth");
        if (liElDepth > 0) {
            var liElParent = getMenuItemParent(liEl);
            if (liElParent) {
                if (liElParent.attr("data-depth") == 0) {
                    liEl.children('input[data-name="parent_id"]').val("");
                }
                else {
                    var liElParentParent = getMenuItemParent(liElParent);
                    if (liElParentParent) {
                        var liElParentParentId = liElParentParent.children('input[data-name="id"]').val();
                        liEl.children('input[data-name="parent_id"]').val(liElParentParentId);
                    }
                    else {
                        liEl.children('input[data-name="parent_id"]').val("");
                    }
                }
                liEl.removeClass("menu-item-depth-" + liElDepth);
                liElDepth--;
                liEl.attr("data-depth", liElDepth);
                liEl.addClass("menu-item-depth-" + liElDepth);
            }
            setAllShownMoveMenu();
        }
    });

    $("#save-btn").parents("form:first").on("beforeSubmit", function () {
        var itemEl = null, itemDataEl = null, content = '';
        menuItemList.children("li").each(function (index) {
            itemEl = $(this);
            itemEl.find("input[data-name]").each(function () {
                itemDataEl = $(this);
                content = content + '<input type="hidden"  name="MenuForm[menu_items][' + index + '][' + itemDataEl.attr("data-name") + ']" value="' + itemDataEl.val() + '"/>';
            });
            content = content + '<input type="hidden"  name="MenuForm[menu_items][' + index + '][depth]" value="' + itemEl.attr("data-depth") + '"/>';
        });
        $("#menu-items").html(content);
    });

    function setAllShownMoveMenu() {
        menuItemList.children("li.menu-item-state-shown").each(function (index) {
            setMoveMenu($(this));
        });
    }

    function setMoveMenu(liEl) {
        var liElDepth = liEl.attr("data-depth");
        var prevEl = liEl.prev("li");
        var canMove = false;

        if (prevEl.length > 0 && prevEl.attr("data-depth") == liElDepth) {
            liEl.find(".menu-item-up-btn").removeClass("hidden");
            canMove = true;
        }
        else {
            liEl.find(".menu-item-up-btn").addClass("hidden");
        }

        var nextEl = liEl.next("li");
        if (nextEl.length > 0 && nextEl.attr("data-depth") == liElDepth) {
            liEl.find(".menu-item-down-btn").removeClass("hidden");
            canMove = true;
        }
        else {
            liEl.find(".menu-item-down-btn").addClass("hidden");
        }

        if (prevEl.length > 0 && liElDepth == prevEl.attr("data-depth") && liElDepth < maxDepth) {
            liEl.find(".menu-item-under-btn").removeClass("hidden");
            canMove = true;
        }
        else {
            liEl.find(".menu-item-under-btn").addClass("hidden");
        }

        if (liElDepth > 0) {
            liEl.find(".menu-item-out-from-under-btn").removeClass("hidden");
            canMove = true;
        }
        else {
            liEl.find(".menu-item-out-from-under-btn").addClass("hidden");
        }

        if (canMove) {
            liEl.find(".menu-item-can-move").removeClass("hidden");
        }
        else {
            liEl.find(".menu-item-can-move").addClass("hidden");
        }
    }

    function getMenuItemParent(liEl) {
        var liElDepth = liEl.attr("data-depth");
        var itemEl = null, menuItemParent = null;
        liEl.prevAll("li").each(function (index) {
            itemEl = $(this);
            if (itemEl.attr("data-depth") < liElDepth) {
                menuItemParent = itemEl;
                return;
            }
        });
        return menuItemParent;
    }

    function getMenuItemEntitySearchList(query, callback) {
        var data = {};
        data[yii.getCsrfParam()] = yii.getCsrfToken();
        data["site_id"] = site_id.val();
        data["type"] = menuItemTypeSelector.val();
        data["name"] = menuItemEntitySearch.val();

        $.ajax({
            url: "/admin/menu/get-entity-list-by-name?site_id=" + site_id.val(),
            type: 'POST',
            data: data
        }).done(function (data) {
            if (data) {
                callback(data);
            }
        });
    }

    function typeHeadAfterSelect(item) {
        $("#menu-item-entity-list-search").append(addEntityItem(item));
        menuItemEntitySearch.val("");
    }

    menuItemEntitySearch.typeahead({
        source: getMenuItemEntitySearchList,
        afterSelect: typeHeadAfterSelect
    });

    function addMenuItems(menuItems) {
        var data = {};
        data[yii.getCsrfParam()] = yii.getCsrfToken();
        data["site_id"] = site_id.val();
        data["menu_items"] = menuItems;

        $.ajax({
            url: "/admin/menu/menu-item?site_id=" + site_id.val(),
            type: 'POST',
            data: data
        }).done(function (html) {
            $("#menu-item-list").append(html);
            addMenuItemBtn.progressButton("stop");
        });
    }

    menuItemTypeSelector.on("change", function () {
        var menuItemEntityGroup = $("#menu-item-entity-group");
        var menuItemUrlGroup = $("#menu-item-url-group");
        var menuItemTextGroup = $("#menu-item-name-group");
        var menuItemType = menuItemTypeSelector.val();
        if (menuItemType < 4) {
            menuItemUrlGroup.addClass("hidden");
            menuItemTextGroup.addClass("hidden");
            menuItemEntityGroup.removeClass("hidden");

            getEntityList();
        }
        else {
            $("#menu-item-url").attr("placeholder", menuItemTypeSelector.find('option:selected').text());
            if (menuItemType == 6) {
                menuItemEntityGroup.addClass("hidden");
                menuItemUrlGroup.addClass("hidden");
                menuItemTextGroup.removeClass("hidden");
            }
            else {
                menuItemEntityGroup.addClass("hidden");
                menuItemUrlGroup.removeClass("hidden");
                menuItemTextGroup.removeClass("hidden");
            }
        }
    });

    function getEntityList() {
        menuItemEntityList.html('<li><div class="checkbox"><label><i class="fa fa-spinner fa-spin"></i></label></div></li>');
        var data = {};
        data[yii.getCsrfParam()] = yii.getCsrfToken();
        data["site_id"] = site_id.val();
        data["type"] = menuItemTypeSelector.val();

        $.ajax({
            url: "/admin/menu/get-entity-list?site_id=" + site_id.val(),
            type: "POST",
            data: data
        }).done(function (data) {
            var content = '';
            for (var i = 0, length = data.length; i < length; i++) {
                content = content + addEntityItem(data[i]);
            }
            menuItemEntityList.html(content);
        });
    }

    function addEntityItem(item) {
        var content = '<li><div class="checkbox"><label>';
        content = content + '<input type="checkbox" data-menu-item-entity-id="' + item.id + '" data-menu-item-entity-name="' + item.name + '"> ' + item.name;
        content = content + '</label></div></li>';
        return content;
    }

    function validateNewMenuItem(menuItemType) {
        var isValid = false;
        if (menuItemType == 6) {
            isValid = required($("#menu-item-name"));
        }
        else {
            isValid = required($("#menu-item-name")) & required($("#menu-item-url"));
        }
        return isValid;
    }

    function required(el) {
        if (el.val().length == 0) {
            el.parent().addClass("has-error");
            return false;
        } else {
            el.parent().removeClass("has-error");
            return true;
        }
    }
});



