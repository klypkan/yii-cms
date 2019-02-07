(function ($) {
    $.fn.progressButton = function (options) {
        var el = this;
        if (options === "stop") {
            el.prop("disabled", false);
            el.children(".btn-progress").remove();
            el.children().removeClass("hidden");
        }
        else {
            el.prop("disabled", true);
            el.children(".btn-progress").remove();
            var content = '<span class="btn-progress">';
            var children = el.children();
            if (children.length > 0) {
                children.addClass("hidden");
            }
            else {
                content = content + '&nbsp;';
            }
            content = content + '<i class="fa fa-spinner fa-spin btn-progress"></i></span>';
            el.append(content);
        }
        return this;
    };
}(jQuery));