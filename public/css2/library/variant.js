(function ($) {
    "use strict";
    var HT = {};

    HT.toggleVariant = function () {
        $("#variantCheckbox").on("change", function () {
            if ($(this).is(":checked")) {
                $(".variant-wrapper").removeClass("hidden");
            } else {
                $(".variant-wrapper").addClass("hidden");
            }
        });
    };

    HT.updateAddButton = function () {
        let total = $(".variant-item").length;
        if (total >= attributeCategory.length) {
            $(".add-variant").hide();
        } else {
            $(".add-variant").show();
        }
    };

    HT.filterOptions = function () {
        let selectedIds = [];
        $(".choose-attribute").each(function () {
            if ($(this).val()) selectedIds.push($(this).val());
        });

        $(".choose-attribute").each(function () {
            let currentVal = $(this).val();
            let options = `<option value="">Chọn nhóm thuộc tính</option>`;
            attributeCategory.forEach((category) => {
                if (category.attribute_category_language.length > 0) {
                    let id = category.id.toString();
                    if (!selectedIds.includes(id) || id === currentVal) {
                        options += `<option value="${id}" ${
                            id === currentVal ? "selected" : ""
                        }>${
                            category.attribute_category_language[0].name
                        }</option>`;
                    }
                }
            });
            $(this).html(options);
        });
    };

    HT.addVariant = function () {
        $(document).on("click", ".add-variant", function () {
            let total = $(".variant-item").length;
            if (total >= attributeCategory.length) return;

            let options = `<option value="">Chọn nhóm thuộc tính</option>`;
            attributeCategory.forEach((category) => {
                if (category.attribute_category_language.length > 0) {
                    options += `<option value="${category.id}">${category.attribute_category_language[0].name}</option>`;
                }
            });

            var item = `
                <div class="row mb20 variant-item">
                    <div class="col-lg-3">
                        <div class="attribute-category">
                            <select name="attribute_category_id" class="choose-attribute form-control">
                                ${options}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <select name="attribute_value_id[]" class="choose-value form-control" multiple="multiple" style="width:100%"></select>
                    </div>
                    <div class="col-lg-1">
                        <button type="button" class="remove-attribute btn btn-danger">Xóa</button>
                    </div>
                </div>
            `;
            $(".variant-body").append(item);

            $(".variant-body .choose-value").last().select2({
                placeholder: "Chọn giá trị thuộc tính",
                allowClear: true,
            });

            HT.updateAddButton();
            HT.filterOptions();
        });
    };

    HT.removeVariant = function () {
        $(document).on("click", ".remove-attribute", function () {
            $(this).closest(".variant-item").remove();
            HT.updateAddButton();
            HT.filterOptions();
            HT.generateVariantTable(); // update bảng
        });
    };

    HT.onChangeSelect = function () {
        // Thay đổi category
        $(document).on("change", ".choose-attribute", function () {
            HT.filterOptions();
            let selectValue = $(this).val();
            let $valueSelect = $(this)
                .closest(".variant-item")
                .find(".choose-value");

            if (selectValue) {
                $.ajax({
                    url: "/get-attribute-values/" + selectValue,
                    type: "GET",
                    success: function (data) {
                        $valueSelect.empty();
                        data.forEach(function (item) {
                            $valueSelect.append(new Option(item.name, item.id));
                        });
                        $valueSelect.trigger("change");
                    },
                });
            } else {
                $valueSelect.empty();
                $valueSelect.trigger("change");
            }
        });

        // Thay đổi value
        $(document).on("change", ".choose-value", function () {
            HT.generateVariantTable();
        });
    };

    HT.slugify = function (str) {
        return str
            .normalize("NFD") // bỏ dấu tiếng Việt
            .replace(/[\u0300-\u036f]/g, "")
            .toUpperCase()
            .replace(/\s+/g, "_") // khoảng trắng -> _
            .replace(/[^A-Z0-9_]/g, ""); // bỏ ký tự đặc biệt
    };

    // Hàm viết tắt tên sản phẩm
    HT.abbreviateName = function (str) {
        let words = str
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toUpperCase()
            .split(/\s+/)
            .filter((w) => w.length > 0);

        if (words.length === 0) return "SP"; // fallback

        // Từ đầu tiên lấy nguyên
        let firstWord = HT.slugify(words[0]);

        // Các từ sau lấy chữ cái đầu
        let initials = words
            .slice(1)
            .map((w) => w[0])
            .join("");

        return firstWord + initials;
    };

    // Sinh SKU từ tên sản phẩm + thuộc tính
    HT.generateSkuFromCombo = function (productName, combo) {
        let base = HT.abbreviateName(productName);
        let attrs = combo.map((val) => HT.slugify(val)).join("-");
        return `${base}-${attrs}`;
    };

    HT.generateVariantTable = function () {
        let $tbody = $(".variantTable tbody");

        // lưu dữ liệu cũ
        let oldData = {};
        $tbody.find("tr").each(function () {
            let $tds = $(this).find("td");

            // tạo key từ ID trong input hidden thay vì text name
            let ids = $(this)
                .find('input[name="variant_attributes[id][]"]')
                .val();
            let key = ids.split(",").join("|");

            oldData[key] = {
                quantity: $tds.find('input[name="variant_quantity[]"]').val(),
                price: $tds.find('input[name="variant_price[]"]').val(),
                sku: $tds.find('input[name="variant_sku[]"]').val(),
                images: $tds
                    .find(".variant-thumb")
                    .map((i, img) => $(img).attr("src"))
                    .get(),
            };
        });

        $tbody.empty();

        // lấy giá chung
        let basePrice = $('input[name="base_price"]').val() || 0;

        // lấy thuộc tính
        let attributes = [];
        $(".variant-item").each(function () {
            let category = $(this)
                .find(".choose-attribute option:selected")
                .text();
            let values = $(this).find(".choose-value").val();
            if (category && values && values.length > 0) {
                let vals = values.map((val) => {
                    return {
                        id: val, // ID chính là value trong <option>
                        name: $(this)
                            .find(`.choose-value option[value="${val}"]`)
                            .text(),
                    };
                });
                attributes.push({ category, values: vals });
            }
        });

        if (attributes.length === 0) return;

        function cartesian(arr) {
            return arr.reduce(
                (a, b) => a.flatMap((d) => b.values.map((e) => [...d, e])),
                [[]]
            );
        }

        let combos = cartesian(attributes);

        let $thead = $(".variantTable thead tr");
        $thead.empty();
        $thead.append(`<th>Hình ảnh</th>`);
        attributes.forEach((attr) => {
            $thead.append(
                `<th style="text-align:center">${attr.category}</th>`
            );
        });
        $thead.append(`<th style="text-align:center">Số lượng</th>`);
        $thead.append(`<th style="text-align:center">Giá tiền</th>`);
        $thead.append(`<th>SKU</th>`);
        $thead.append(
            `<th style="text-align:center; width:100px">Hành động</th>`
        );

        combos.forEach((combo) => {
            // key dựa trên ID
            let key = combo.map((c) => c.id).join("|");

            let data = oldData[key] || {
                quantity: 0,
                price: basePrice,
                sku: HT.generateSkuFromCombo(
                    $('input[name="name"]').val() || "SP",
                    combo.map((c) => c.name) // SKU vẫn dựa trên tên
                ),
                images: [],
            };

            // ảnh: input file gắn SKU
            let imageCell = `
        <td>
            <div class="variant-image-wrapper" style="display:flex; gap:5px; flex-wrap:wrap; justify-content:flex-start; max-width:220px;">
                ${
                    data.images.length > 0
                        ? data.images
                              .map(
                                  (src) =>
                                      `<img src="${src}" style="width:70px;height:70px;object-fit:cover;cursor:pointer;margin-bottom:5px;" class="variant-thumb">`
                              )
                              .join("")
                        : `<img src="https://i.pinimg.com/564x/0c/bb/aa/0cbbaab0deff7f188a7762d9569bf1b3.jpg" style="width:70px;height:70px;object-fit:cover;cursor:pointer;margin-bottom:5px;" class="variant-thumb">`
                }
                <input type="file" 
                    name="variant_image[${data.sku}][]" 
                    class="variant-image-input" 
                    style="display:none" 
                    multiple>
            </div>
        </td>`;

            let row = `<tr> ${imageCell}`;

            let names = [];
            let ids = [];

            combo.forEach((valObj) => {
                names.push(valObj.name);
                ids.push(valObj.id);
                row += `
                    <td style="text-align:center">
                        ${valObj.name}
                    </td>`;
            });

            // tạo hidden input gom chung
            row += `
                <input type="hidden" name="variant_attributes[name][]" value="${names.join(
                    ", "
                )}">
                <input type="hidden" name="variant_attributes[id][]" value="${ids.join(
                    ","
                )}">
            `;
            row += `
            <td style="text-align:center; width:100px">
                <input type="number" name="variant_quantity[]" value="${data.quantity}" class="form-control form-control-sm">
            </td>
            <td style="text-align:center; width:100px">
                <input type="text" name="variant_price[]" value="${data.price}" class="form-control form-control-sm">
            </td>
            <td style="width:100px">
                <input type="text" name="variant_sku[]" value="${data.sku}" class="form-control form-control-sm">
            </td>
            <td style="text-align:center">
                <button type="button" class="btn btn-danger btn-sm remove-variant-row">Xóa</button>
            </td>
        </tr>`;

            $tbody.append(row);
        });
    };

    HT.loadVariants = function (variants, callback) {
        $(".variant-body").empty();

        // map category_id -> value đã chọn
        let categoryMap = {};
        variants.forEach((v) => {
            v.category_id.forEach((catId, index) => {
                if (!categoryMap[catId]) categoryMap[catId] = [];
                let valId = v.value_id[index];
                let valName = v.name[index].trim();

                if (!categoryMap[catId].some((x) => x.id == valId)) {
                    categoryMap[catId].push({ id: valId, name: valName });
                }
            });
        });

        let ajaxCalls = [];

        Object.keys(categoryMap).forEach((catId) => {
            let category = attributeCategory.find((c) => c.id == catId);
            if (!category) return;

            let item = `
            <div class="row mb20 variant-item">
                <div class="col-lg-3">
                    <div class="attribute-category">
                        <select name="attribute_category_id" class="choose-attribute form-control">
                            <option value="${category.id}" selected>
                                ${category.attribute_category_language[0].name}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-8">
                    <select name="attribute_value_id[]" 
                            class="choose-value form-control" 
                            multiple="multiple" 
                            style="width:100%">
                    </select>
                </div>
                <div class="col-lg-1">
                    <button type="button" class="remove-attribute btn btn-danger">Xóa</button>
                </div>
            </div>
        `;

            $(".variant-body").append(item);
            let $valueSelect = $(".variant-body .choose-value").last();

            // gọi AJAX để load tất cả value
            let ajax = $.ajax({
                url: "/get-attribute-values/" + catId,
                type: "GET",
                success: function (data) {
                    $valueSelect.empty();
                    data.forEach(function (item) {
                        // check nếu đã có trong DB thì select
                        let isSelected = categoryMap[catId].some(
                            (v) => v.id == item.id
                        );
                        $valueSelect.append(
                            new Option(
                                item.name,
                                item.id,
                                isSelected,
                                isSelected
                            )
                        );
                    });
                    $valueSelect.trigger("change");
                },
            });

            ajaxCalls.push(ajax);

            $valueSelect.select2({
                placeholder: "Chọn giá trị thuộc tính",
                allowClear: true,
            });
        });

        // chỉ gọi generateTable sau khi load xong hết ajax
        $.when.apply($, ajaxCalls).done(function () {
            HT.updateAddButton();
            HT.filterOptions();
            HT.generateVariantTable();
            if (typeof callback === "function") callback();
        });
    };

    function loadServerDataToTable() {
        $(".variantTable tbody tr").each(function (i) {
            let $row = $(this);
            let sku = $row.find('input[name="variant_sku[]"]').val(); // lấy SKU

            // quantity, price
            $row.find('input[name="variant_quantity[]"]').val(
                variantQuantity[i] || 0
            );
            $row.find('input[name="variant_price[]"]').val(
                variantPrice[i] || 0
            );

            // images
            let $wrapper = $row.find(".variant-image-wrapper");
            $wrapper.find(".variant-thumb").remove(); // xóa ảnh cũ

            if (sku && variantImage[sku] && variantImage[sku].length) {
                variantImage[sku].forEach((src) => {
                    let img = `<img src="${src}" style="width:70px;height:70px;object-fit:cover;cursor:pointer;margin-bottom:5px;" class="variant-thumb">`;
                    $wrapper.append(img);
                });
            } else {
                // fallback ảnh mặc định
                let img = `<img src="https://i.pinimg.com/564x/0c/bb/aa/0cbbaab0deff7f188a7762d9569bf1b3.jpg" style="width:70px;height:70px;object-fit:cover;cursor:pointer;margin-bottom:5px;" class="variant-thumb">`;
                $wrapper.append(img);
            }
        });
    }

    $(document).ready(function () {
        HT.toggleVariant();
        HT.addVariant();
        HT.removeVariant();
        HT.onChangeSelect();
        HT.updateAddButton();
        $(document).on("click", ".remove-variant-row", function () {
            $(this).closest("tr").remove();
        });

        //image
        $(document).on("click", ".variant-thumb", function () {
            $(this).siblings(".variant-image-input").click();
        });
        //preview image
        $(document).on("change", ".variant-image-input", function () {
            let input = this;
            let $wrapper = $(this).closest(".variant-image-wrapper");
            $wrapper.find(".variant-thumb").remove(); // Xóa thumbnail cũ

            if (input.files) {
                Array.from(input.files).forEach((file) => {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        let img = `<img src="${e.target.result}" 
                    style="width:70px;height:70px;object-fit:cover;cursor:pointer;margin-bottom:5px;" 
                    class="variant-thumb">`;
                        $wrapper.append(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
        $(document).on("input", 'input[name="base_price"]', function () {
            let val = $(this).val();
            $('.variantTable input[name="variant_price[]"]').val(val);
        });

        HT.loadVariants(variantData, function () {
            loadServerDataToTable();
        });
    });
})(jQuery);
