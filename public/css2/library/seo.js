(function ($) {
    "use strict";
    var HT = {};
    HT.seoPreview = () => {
        $("input[name=meta_title]").on("keyup", function () {
            let input = $(this);
            let value = input.val().trim();
            $(".meta-title").html(value);
        });

        $("input[name=canonical]").on("keyup", function () {
            let input = $(this);
            let value = input.val().trim();
            $(".canonical").html("https://app-ecommerce.test/" + value);
        });

        $("textarea[name=meta_description]").on("keyup", function () {
            let input = $(this);
            let value = input.val().trim();
            $(".meta-description").html(value);
        });
    };

    $(document).ready(function () {
        HT.seoPreview();
    });
})(jQuery);
