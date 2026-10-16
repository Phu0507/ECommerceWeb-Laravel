(function ($) {
    ("use strict");
    var HT = {};

    HT.changeDateTimeRange = function () {
        $(".start-date, .end-date").on("change", function () {
            let startDate = $(".start-date").val();
            let endDate = $(".end-date").val();
            if (startDate && endDate) {
                let start = new Date(startDate);
                let end = new Date(endDate);
                if (start > end) {
                    alert("Ngày bắt đầu không được lớn hơn ngày kết thúc");
                    $(this).val("");
                }
            }
        });
    };

    HT.toggleCustomerOption = function () {
        $(".choose-option").on("change", function () {
            if ($("#custom_customers").is(":checked")) {
                $("#customer_select_box").show();
            } else {
                $("#customer_select_box").hide();
                $("#customer_select").val(null).trigger("change");
                $("#customer_custom_boxes").empty();
            }
        });
    };

    HT.renderBox1 = function (val, label) {
        return `
        <div class="ibox" id="box-${val}">
            <div class="ibox-content">
                <label>Chọn nhóm khách hàng:</label>
                <select name="customer_group[]" 
                        class="form-control select2-customer-group" 
                        multiple="multiple" style="width: 100%">
                </select>
            </div>
        </div>
    `;
    };

    HT.renderBox2 = function (val, label) {
        return `
        <div class="ibox" id="box-${val}">
            <div class="ibox-content">
                <label>Chọn giới tính:</label>
                <select name="gender" class="form-control">
                    <option value="male" selected>Nam</option>
                    <option value="female">Nữ</option>
                </select>
            </div>
        </div>
    `;
    };

    HT.renderBox3 = function (val, label) {
        return `
        <div class="ibox" id="box-${val}">
            <div class="ibox-content">
                <label>Chọn nhóm độ tuổi:</label>
                <select name="age_group[]" 
                        class="form-control select2-age-group" 
                        multiple="multiple" style="width: 100%">
                    <option value="<18">Dưới 18</option>
                    <option value="18-25">18 - 25</option>
                    <option value="26-35">26 - 35</option>
                    <option value="36-50">36 - 50</option>
                    <option value=">50">Trên 50</option>
                </select>
            </div>
        </div>
    `;
    };

    HT.initCustomerSelect = function () {
        $(".select2-customer").select2({
            placeholder: "Chọn đối tượng khách hàng",
            allowClear: true,
            width: "100%",
        });

        $(".select2-customer").on("change", function () {
            let selected = $(this).val() || [];
            let $container = $("#customer_custom_boxes");
            selected.forEach(function (val) {
                if ($("#box-" + val).length === 0) {
                    let label = $(
                        "#customer_select option[value='" + val + "']"
                    ).text();
                    let div = "";

                    if (val == "user_role") div = HT.renderBox1(val, label);
                    if (val == "gender") div = HT.renderBox2(val, label);
                    if (val == "age_group") div = HT.renderBox3(val, label);
                    $container.append(div);
                    // Nếu là Box1 thì khởi tạo select2 ajax
                    if (val == "user_role") {
                        $("#box-" + val)
                            .find(".select2-customer-group")
                            .select2({
                                placeholder: "Chọn nhóm khách hàng",
                                allowClear: true,
                                width: "100%",
                                ajax: {
                                    url: "/ajax-get-userRoles",
                                    dataType: "json",
                                    processResults: function (data) {
                                        return {
                                            results: data.map(function (item) {
                                                return {
                                                    id: item.id,
                                                    text: item.name,
                                                };
                                            }),
                                        };
                                    },
                                    cache: true,
                                },
                            });
                    }

                    if (val == "age_group") {
                        $("#box-" + val)
                            .find(".select2-age-group")
                            .select2({
                                placeholder: "Chọn nhóm độ tuổi",
                                allowClear: true,
                                width: "100%",
                            });
                    }
                }
            });

            // Xóa box của option bỏ chọn
            $container.children(".ibox").each(function () {
                let id = $(this).attr("id").replace("box-", "");
                if (!selected.includes(id)) {
                    $(this).remove();
                }
            });
        });
    };

    // Giảm giá cho tổng đơn
    HT.renderPromotionOrder = function (discounts = [], discountType) {
        let rowsHtml = "";

        if (discounts.length > 0) {
            discounts.forEach((discount, index) => {
                rowsHtml += `
                <tr>
                    <td class="text-right">
                        <input type="number" style="height: 30px" 
                            name="order_amount_from[]" 
                            value="${discount.order_amount_from ?? 0}">
                    </td>
                    <td class="text-right">
                        <input type="number" style="height: 30px" 
                            name="order_amount_to[]" 
                            value="${discount.order_amount_to ?? ""}">
                    </td>
                    <td class="text-right">
                        <div class="uk-flex uk-flex-middle">
                            <input type="number" style="height: 30px" 
                                name="discount[]" 
                                value="${discount.discount_value ?? ""}">
                            <select style="height: 30px" name="discount_type">
                                <option value="percentage" ${
                                    discountType === "percentage"
                                        ? "selected"
                                        : ""
                                }>%</option>
                                <option value="fixed" ${
                                    discountType === "fixed" ? "selected" : ""
                                }>đ</option>
                            </select>
                        </div>
                    </td>
                    <td class="text-center"></td>
                </tr>
            `;
            });
        } else {
            // Trường hợp tạo mới thì vẫn có 1 row mặc định
            rowsHtml = `
            <tr>
                <td class="text-right">
                    <input type="number" style="height: 30px" name="order_amount_from[]" value="0">
                </td>
                <td class="text-right">
                    <input type="number" style="height: 30px" name="order_amount_to[]">
                </td>
                <td class="text-right">
                    <div class="uk-flex uk-flex-middle">
                        <input type="number" style="height: 30px" name="discount[]">
                        <select style="height: 30px" name="discount_type">
                            <option value="percentage">%</option>
                            <option value="fixed">đ</option>
                        </select>
                    </div>
                </td>
                <td class="text-center"></td>
            </tr>
        `;
        }

        return `
        <div class="order_amount_range">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-right">Giá trị từ</th>
                        <th class="text-right">Giá trị đến</th>
                        <th class="text-right">Giảm giá</th>
                    </tr>
                </thead>
                <tbody>
                    ${rowsHtml}
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-primary add_row" disabled>Thêm khoảng giá</button>
        </div>
    `;
    };

    HT.initPromotionOrder = function () {
        // Hàm cập nhật nút xóa
        function updateRemoveButtons($tbody) {
            $tbody.find(".remove_row").remove(); // xóa tất cả nút cũ
            let $rows = $tbody.find("tr");
            if ($rows.length > 1) {
                $rows
                    .last()
                    .find("td:last")
                    .append(
                        '<button type="button" class="btn btn-sm btn-danger remove_row">X</button>'
                    );
            }
        }

        // Hàm cập nhật trạng thái nút thêm
        function updateAddButtonState($container) {
            let $tbody = $container.find("tbody");
            let lastToVal = $tbody
                .find("tr")
                .last()
                .find("input[name='order_amount_to[]']")
                .val();
            let $addBtn = $container.find(".add_row");
            if (lastToVal && lastToVal !== "") {
                $addBtn
                    .prop("disabled", false)
                    .removeClass("btn-secondary")
                    .addClass("btn-primary");
            } else {
                $addBtn
                    .prop("disabled", true)
                    .removeClass("btn-primary")
                    .addClass("btn-secondary");
            }
        }

        // Thêm row
        $(document).on("click", "#promotion_custom_box .add_row", function (e) {
            e.preventDefault();

            let $container = $(this).closest(".order_amount_range");
            let $table = $container.find("table");
            let $tbody = $table.find("tbody");
            let $lastRow = $tbody.find("tr").last();

            let lastTo = parseInt(
                $lastRow.find('input[name="order_amount_to[]"]').val()
            );
            if (isNaN(lastTo)) lastTo = 0;
            let newFrom = lastTo + 1;

            let newRow = $(`
            <tr>
                <td class="text-right">
                    <input type="number" style="height: 30px" name="order_amount_from[]" value="${newFrom}">
                </td>
                <td class="text-right">
                    <input type="number" style="height: 30px" name="order_amount_to[]">
                </td>
                <td class="text-right">
                    <div class="uk-flex uk-flex-middle">
                        <input type="number" style="height: 30px" name="discount[]">
                        <select style="height: 30px" name="discount_type">
                            <option value="percentage">%</option>
                            <option value="fixed">đ</option>
                        </select>
                    </div>
                </td>
                <td class="text-center"></td>
            </tr>
        `);

            $tbody.append(newRow);
            updateRemoveButtons($tbody);
            updateAddButtonState($container);
        });

        // Xóa row
        $(document).on(
            "click",
            "#promotion_custom_box .remove_row",
            function (e) {
                e.preventDefault();
                let $container = $(this).closest(".order_amount_range");
                let $table = $container.find("table");
                let $tbody = $table.find("tbody");

                $(this).closest("tr").remove();
                updateRemoveButtons($tbody);
                updateAddButtonState($container);
            }
        );

        // Kiểm tra khi thay đổi input 'to'
        $(document).on("input", "input[name='order_amount_to[]']", function () {
            let $container = $(this).closest(".order_amount_range");
            updateAddButtonState($container);
        });

        // Khởi tạo trạng thái ban đầu
        $(".order_amount_range").each(function () {
            updateAddButtonState($(this));
        });
    };

    //Giảm giá cho từng sản phẩm
    HT.renderPromotionProduct = function (
        promotionDiscounts = [],
        promotionProducts = [],
        discountType
    ) {
        let type = "products";
        let selectedIds = [];
        let minQuantity = 0;
        let maxQuantity = "";
        let discountValue = "";

        // lấy discount
        if (promotionDiscounts.length > 0) {
            let d = promotionDiscounts[0];
            minQuantity = d.min_quantity || 0;
            maxQuantity = d.max_quantity || "";
            discountValue = d.discount_value || "";
        }

        // xác định type (All, products, category)
        if (promotionProducts.length > 0) {
            if (promotionProducts[0].apply_all == 1) {
                type = "All";
            } else if (promotionProducts.some((p) => p.category_id)) {
                type = "category";
                selectedIds = promotionProducts
                    .map((p) => p.category_id)
                    .filter(Boolean);
            } else if (promotionProducts.some((p) => p.product_id)) {
                type = "products";
                selectedIds = promotionProducts
                    .map((p) => p.product_id)
                    .filter(Boolean);
            }
        }

        return `
    <div class="product_amount_range">
        <div class="choose-module mt20">
            <select name='product-range-select' class="form-control product-range-select" id="product-range-select">
                <option value="products" ${
                    type === "products" ? "selected" : ""
                }>Sản phẩm đơn lẻ</option>
                <option value="All" ${
                    type === "All" ? "selected" : ""
                }>Toàn bộ sản phẩm</option>
                <option value="category" ${
                    type === "category" ? "selected" : ""
                }>Loại sản phẩm</option>
            </select>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-right">Sản phẩm áp dụng</th>
                    <th class="text-right">SL tối thiểu</th>
                    <th class="text-right">Giới hạn KM</th>
                    <th class="text-right">Giảm giá</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-right" style="width: 390px">
                        <select name="ids[]" class="form-control select2-products" multiple>
                            ${selectedIds
                                .map(
                                    (id) =>
                                        `<option value="${id}" selected>${id}</option>`
                                )
                                .join("")}
                        </select>
                    </td>
                    <td class="text-right">
                        <input type="number" name="min_quantity" value="${minQuantity}" style="height:30px; width:60px;">
                    </td>
                    <td class="text-right">
                        <input type="number" name="max_quantity" value="${maxQuantity}" style="height:30px; width:100px;">
                    </td>
                    <td class="text-right">
                        <div class="uk-flex uk-flex-middle">
                            <input type="number" name="discount[]" value="${discountValue}" style="height:30px; width:100px;">
                            <select name="discount_type" style="height:30px">
                                <option value="percentage" ${
                                    discountType === "percentage"
                                        ? "selected"
                                        : ""
                                }>%</option>
                                <option value="fixed" ${
                                    discountType === "fixed" ? "selected" : ""
                                }>đ</option>
                            </select>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>`;
    };

    HT.initPromotionProduct = function () {
        let $selectType = $("#product-range-select");
        let $selectProducts = $(".select2-products");

        function initSelect2Products() {
            $selectProducts.select2({
                placeholder: "Chọn sản phẩm",
                allowClear: true,
                width: "100%",
                ajax: {
                    url: "/ajax-search-products",
                    dataType: "json",
                    delay: 250,
                    data: (params) => ({ q: params.term }),
                    processResults: (data) => ({
                        results: data.map((item) => ({
                            id: item.id,
                            text: item.product_name,
                        })),
                    }),
                    cache: true,
                },
                minimumInputLength: 2,
            });
        }

        function initSelect2Categories() {
            $selectProducts.select2({
                placeholder: "Chọn loại sản phẩm",
                allowClear: true,
                width: "100%",
                ajax: {
                    url: "/ajax-get-categories",
                    dataType: "json",
                    processResults: (data) => ({
                        results: data.map((item) => ({
                            id: item.id,
                            text: item.name,
                        })),
                    }),
                    cache: true,
                },
                minimumInputLength: 0,
            });
        }

        function resetSelect2() {
            if ($selectProducts.hasClass("select2-hidden-accessible")) {
                $selectProducts.select2("destroy");
            }
            $selectProducts.show();
        }

        $selectType.on("change", function () {
            const value = $(this).val();
            resetSelect2();

            if (value === "products") {
                initSelect2Products();
            } else if (value === "category") {
                initSelect2Categories();
            } else if (value === "All") {
                $selectProducts.hide();
            }
        });

        // trigger để init đúng theo value đã chọn
        $selectType.trigger("change");
    };

    HT.togglePromotionType = function () {
        $(".promotion-select").on("change", function () {
            let val = $(this).val();
            let $container = $("#promotion_custom_box");
            $container.empty();

            if (val === "order") {
                $container.append(
                    HT.renderPromotionOrder(
                        promotionDiscounts || [],
                        discountType || ""
                    )
                );
            }
            if (val === "product") {
                $container.append(
                    HT.renderPromotionProduct(
                        promotionDiscounts || [],
                        promotionProducts || [],
                        discountType || ""
                    )
                );
                HT.initPromotionProduct();
            }
        });

        // Khi load trang, nếu đang ở edit thì tự động trigger
        let currentVal = $(".promotion-select").val();
        if (currentVal) {
            $(".promotion-select").trigger("change");
        }
    };

    HT.validateOrderAmount = function () {
        // Khi thay đổi giá trị 'from' hoặc 'to'
        $(document).on(
            "change",
            "input[name='order_amount_from[]'], input[name='order_amount_to[]']",
            function () {
                let $table = $(this).closest("table");
                let $rows = $table.find("tbody tr");

                $rows.each(function (index) {
                    let fromVal =
                        parseInt(
                            $(this)
                                .find("input[name='order_amount_from[]']")
                                .val()
                        ) || 0;
                    let toVal =
                        parseInt(
                            $(this)
                                .find("input[name='order_amount_to[]']")
                                .val()
                        ) || 0;

                    // Kiểm tra row hiện tại: to > from
                    if (toVal && toVal <= fromVal) {
                        alert(
                            "Giá trị kết thúc phải lớn hơn giá trị bắt đầu trong cùng một hàng."
                        );
                        $(this).find("input[name='order_amount_to[]']").val("");
                        return false; // ngăn lặp tiếp row này
                    }

                    // Kiểm tra liên row: from >= to row trước
                    if (index > 0) {
                        let prevTo =
                            parseInt(
                                $rows
                                    .eq(index - 1)
                                    .find("input[name='order_amount_to[]']")
                                    .val()
                            ) || 0;
                        if (fromVal < prevTo) {
                            alert(
                                `Giá trị bắt đầu của hàng ${
                                    index + 1
                                } không thể nhỏ hơn giá trị kết thúc của hàng trước.`
                            );
                            $(this)
                                .find("input[name='order_amount_from[]']")
                                .val(prevTo + 1);
                            return false;
                        }
                    }
                });
            }
        );
    };
    //edit

    HT.loadOldCustomerConditions = function () {
        let selected = $("#customer_select").val() || [];
        let $container = $("#customer_custom_boxes");
        selected.forEach(function (val) {
            if ($("#box-" + val).length === 0) {
                let label = $(
                    "#customer_select option[value='" + val + "']"
                ).text();
                let div = "";

                if (val === "user_role") {
                    div = HT.renderBox1(val, label);
                    $container.append(div);

                    let $select = $("#box-" + val).find(
                        ".select2-customer-group"
                    );
                    $select.select2({
                        placeholder: "Chọn nhóm khách hàng",
                        allowClear: true,
                        width: "100%",
                        ajax: {
                            url: "/ajax-get-userRoles",
                            dataType: "json",
                            processResults: function (data) {
                                return {
                                    results: data.map(function (item) {
                                        return { id: item.id, text: item.name };
                                    }),
                                };
                            },
                            cache: true,
                        },
                    });

                    // set lại giá trị đã chọn (userRoles từ PHP truyền ra)
                    if (oldUserRoles && oldUserRoles.length > 0) {
                        // load xong mới set
                        let options = oldUserRoles.map(function (id) {
                            return new Option(id, id, true, true);
                        });
                        $select.append(options).trigger("change");
                    }
                }

                if (val === "gender") {
                    div = HT.renderBox2(val, label);
                    $container.append(div);

                    if (oldGenders && oldGenders.length > 0) {
                        $("#box-" + val)
                            .find("select[name='gender']")
                            .val(oldGenders[0]); // chỉ 1 gender
                    }
                }

                if (val === "age_group") {
                    div = HT.renderBox3(val, label);
                    $container.append(div);

                    let $select = $("#box-" + val).find(".select2-age-group");
                    $select.select2({
                        placeholder: "Chọn nhóm độ tuổi",
                        allowClear: true,
                        width: "100%",
                    });

                    if (oldAgeGroups && oldAgeGroups.length > 0) {
                        $select.val(oldAgeGroups).trigger("change");
                    }
                }
            }
        });
    };

    $(document).ready(function () {
        HT.changeDateTimeRange();
        HT.toggleCustomerOption();
        HT.initCustomerSelect();
        HT.togglePromotionType();
        HT.initPromotionOrder();
        HT.validateOrderAmount();
        HT.loadOldCustomerConditions();
    });
})(jQuery);
