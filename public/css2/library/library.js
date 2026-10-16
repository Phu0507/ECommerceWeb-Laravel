(function ($) {
    "use strict";
    var HT = {};

    HT.switchery = () => {
        $(".js-switch").each(function () {
            var elem = this;
            var switchery = new Switchery(elem, {
                color: "#1AB394",
                size: "small",
            });
        });
    };
    HT.changeStatus = () => {
        $(document).on("change", ".status", function () {
            let checkbox = $(this);
            let id = checkbox.data("id");
            let status = checkbox.is(":checked") ? "active" : "inactive";

            // Gửi AJAX để cập nhật trạng thái
            $.ajax({
                url: "/update-status",
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    id: id,
                    status: status,
                },
                success: function (res) {
                    console.log("Cập nhật thành công:", res);
                    // Bạn có thể hiển thị toast hoặc alert tùy ý
                },
                error: function (err) {
                    console.error("Cập nhật thất bại:", err);
                    alert("Có lỗi xảy ra khi cập nhật trạng thái!");
                },
            });
        });
    };
    $(document).ready(function () {
        HT.switchery();
        HT.changeStatus();
    });
})(jQuery);
