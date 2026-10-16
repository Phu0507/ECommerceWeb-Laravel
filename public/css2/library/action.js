$(document).on("click", ".btn-change-status", function () {
    let orderId = $(this).data("id");
    let status = $(this).data("status");
    let token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    $.ajax({
        url: "/orders/" + orderId + "/status",
        type: "POST",
        data: {
            _token: token,
            status: status,
        },
        success: function (res) {
            if (res.success) {
                // cập nhật badge và action
                $("#status-" + orderId).html(res.status_badge);
                $("#actions-" + orderId).html(res.actions_html);
            }
        },
        error: function (xhr) {
            alert(xhr.responseJSON?.error || "Có lỗi xảy ra!");
        },
    });
});
