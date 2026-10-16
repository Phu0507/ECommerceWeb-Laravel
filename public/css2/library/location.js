(function ($) {
    "use strict";
    var HT = {};

    // Chọn Tỉnh/Thành phố -> load Quận/Huyện
    HT.province = () => {
        $(document).on("change", ".province", function () {
            let _this = $(this);
            let provinceId = _this.val();

            if (!provinceId) {
                // Reset quận/huyện và xã/phường nếu chưa chọn tỉnh
                _this
                    .closest(".row")
                    .find(".district")
                    .empty()
                    .append('<option value="">[Chọn Quận/Huyện]</option>');
                _this
                    .closest(".row")
                    .find(".ward")
                    .empty()
                    .append('<option value="">[Chọn Xã/Phường]</option>');
                return;
            }

            $.ajax({
                url: "/api/districts",
                type: "GET",
                data: { provinceId: provinceId },
                dataType: "json",
                success: function (data) {
                    let districtSelect = _this
                        .closest(".row")
                        .find(".district");
                    let wardSelect = _this.closest(".row").find(".ward");

                    districtSelect
                        .empty()
                        .append('<option value="">[Chọn Quận/Huyện]</option>');
                    wardSelect
                        .empty()
                        .append('<option value="">[Chọn Xã/Phường]</option>');

                    $.each(data, function (index, district) {
                        districtSelect.append(
                            `<option value="${district.maqh}">${district.name}</option>`
                        );
                    });
                },
            });
        });
    };

    // Chọn Quận/Huyện -> load Xã/Phường
    HT.district = () => {
        $(document).on("change", ".district", function () {
            let _this = $(this);
            let districtId = _this.val();

            if (!districtId) {
                // Reset xã/phường nếu chưa chọn quận
                _this
                    .closest(".row")
                    .find(".ward")
                    .empty()
                    .append('<option value="">[Chọn Xã/Phường]</option>');
                return;
            }

            $.ajax({
                url: "/api/wards",
                type: "GET",
                data: { districtId: districtId },
                dataType: "json",
                success: function (data) {
                    let wardSelect = _this.closest(".row").find(".ward");

                    wardSelect
                        .empty()
                        .append('<option value="">[Chọn Xã/Phường]</option>');
                    $.each(data, function (index, ward) {
                        wardSelect.append(
                            `<option value="${ward.xaid}">${ward.name}</option>`
                        );
                    });
                },
            });
        });
    };

    HT.prefillLocation = () => {
        $(".province").each(function () {
            const provinceSelect = $(this);
            const provinceId = provinceSelect.val();
            const row = provinceSelect.closest(".row");
            const districtSelect = row.find(".district");
            const wardSelect = row.find(".ward");

            if (!provinceId) return;

            $.ajax({
                url: "/api/districts",
                type: "GET",
                data: { provinceId },
                dataType: "json",
                success: function (districts) {
                    const selectedDistrictId =
                        districtSelect.data("selected-id");

                    districtSelect
                        .empty()
                        .append('<option value="">[Chọn Quận/Huyện]</option>');

                    $.each(districts, function (index, district) {
                        districtSelect.append(
                            `<option value="${district.maqh}" ${
                                selectedDistrictId == district.maqh
                                    ? "selected"
                                    : ""
                            }>${district.name}</option>`
                        );
                    });

                    if (selectedDistrictId) {
                        $.ajax({
                            url: "/api/wards",
                            type: "GET",
                            data: { districtId: selectedDistrictId },
                            dataType: "json",
                            success: function (wards) {
                                const selectedWardId =
                                    wardSelect.data("selected-id");

                                wardSelect
                                    .empty()
                                    .append(
                                        '<option value="">[Chọn Xã/Phường]</option>'
                                    );

                                $.each(wards, function (index, ward) {
                                    wardSelect.append(
                                        `<option value="${ward.xaid}" ${
                                            selectedWardId == ward.xaid
                                                ? "selected"
                                                : ""
                                        }>${ward.name}</option>`
                                    );
                                });
                            },
                        });
                    }
                },
            });
        });
    };

    $(document).ready(function () {
        HT.province();
        HT.district();
        HT.prefillLocation();
    });
})(jQuery);
