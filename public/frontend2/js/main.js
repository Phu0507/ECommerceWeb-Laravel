/*  ---------------------------------------------------
    Template Name: Ogani
    Description:  Ogani eCommerce  HTML Template
    Author: Colorlib
    Author URI: https://colorlib.com
    Version: 1.0
    Created: Colorlib
---------------------------------------------------------  */

"use strict";

(function ($) {
    /*------------------
        Preloader
    --------------------*/
    $(window).on("load", function () {
        $(".loader").fadeOut();
        $("#preloder").delay(200).fadeOut("slow");

        /*------------------
            Gallery filter
        --------------------*/
        $(".featured__controls li").on("click", function () {
            $(".featured__controls li").removeClass("active");
            $(this).addClass("active");
        });
        if ($(".featured__filter").length > 0) {
            var containerEl = document.querySelector(".featured__filter");
            var mixer = mixitup(containerEl);
        }
    });

    /*------------------
        Background Set
    --------------------*/
    $(".set-bg").each(function () {
        var bg = $(this).data("setbg");
        $(this).css("background-image", "url(" + bg + ")");
    });

    //Humberger Menu
    $(".humberger__open").on("click", function () {
        $(".humberger__menu__wrapper").addClass(
            "show__humberger__menu__wrapper"
        );
        $(".humberger__menu__overlay").addClass("active");
        $("body").addClass("over_hid");
    });

    $(".humberger__menu__overlay").on("click", function () {
        $(".humberger__menu__wrapper").removeClass(
            "show__humberger__menu__wrapper"
        );
        $(".humberger__menu__overlay").removeClass("active");
        $("body").removeClass("over_hid");
    });

    /*------------------
		Navigation
	--------------------*/
    $(".mobile-menu").slicknav({
        prependTo: "#mobile-menu-wrap",
        allowParentLinks: true,
    });

    /*-----------------------
        Categories Slider
    ------------------------*/
    $(".categories__slider").owlCarousel({
        loop: true,
        margin: 0,
        items: 4,
        dots: false,
        nav: true,
        navText: [
            "<span class='fa fa-angle-left'><span/>",
            "<span class='fa fa-angle-right'><span/>",
        ],
        animateOut: "fadeOut",
        animateIn: "fadeIn",
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: true,
        responsive: {
            0: {
                items: 1,
            },

            480: {
                items: 2,
            },

            768: {
                items: 3,
            },

            992: {
                items: 4,
            },
        },
    });

    $(".hero__categories__all").on("click", function () {
        $(".hero__categories ul").slideToggle(400);
    });

    /*--------------------------
        Latest Product Slider
    ----------------------------*/
    $(".latest-product__slider").owlCarousel({
        loop: true,
        margin: 0,
        items: 1,
        dots: false,
        nav: true,
        navText: [
            "<span class='fa fa-angle-left'><span/>",
            "<span class='fa fa-angle-right'><span/>",
        ],
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: true,
    });

    /*-----------------------------
        Product Discount Slider
    -------------------------------*/
    $(".product__discount__slider").owlCarousel({
        loop: true,
        margin: 0,
        items: 3,
        dots: true,
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: true,
        responsive: {
            320: {
                items: 1,
            },

            480: {
                items: 2,
            },

            768: {
                items: 2,
            },

            992: {
                items: 3,
            },
        },
    });

    /*---------------------------------
        Product Details Pic Slider
    ----------------------------------*/
    $(".product__details__pic__slider").owlCarousel({
        loop: true,
        margin: 20,
        items: 4,
        dots: true,
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: true,
    });

    /*-----------------------
		Price Range Slider
	------------------------ */
    var rangeSlider = $(".price-range"),
        minamount = $("#minamount"),
        maxamount = $("#maxamount"),
        minPrice = rangeSlider.data("min"),
        maxPrice = rangeSlider.data("max");

    // Lấy giá từ input (đã được set từ request) hoặc dùng mặc định
    var currentMin = parseInt(minamount.val()) || minPrice;
    var currentMax = parseInt(maxamount.val()) || maxPrice;

    rangeSlider.slider({
        range: true,
        min: minPrice,
        max: maxPrice,
        values: [currentMin, currentMax], // <-- sửa ở đây
        slide: function (event, ui) {
            minamount.val(ui.values[0]);
            maxamount.val(ui.values[1]);
        },
    });

    // Hiển thị giá trong input
    minamount.val(currentMin);
    maxamount.val(currentMax);

    // Khi nhập vào input thì cập nhật slider
    minamount.on("change keyup", function () {
        var minVal = parseInt($(this).val().replace(/\D/g, "")) || minPrice;
        var maxVal = rangeSlider.slider("values", 1);
        if (minVal < minPrice) minVal = minPrice;
        if (minVal > maxVal) minVal = maxVal;
        rangeSlider.slider("values", [minVal, maxVal]);
    });

    maxamount.on("change keyup", function () {
        var maxVal = parseInt($(this).val().replace(/\D/g, "")) || maxPrice;
        var minVal = rangeSlider.slider("values", 0);
        if (maxVal > maxPrice) maxVal = maxPrice;
        if (maxVal < minVal) maxVal = minVal;
        rangeSlider.slider("values", [minVal, maxVal]);
    });

    /*--------------------------
        Select
    ----------------------------*/
    $("select").niceSelect();

    /*------------------
		Single Product
	--------------------*/
    $(".product__details__pic__slider img").on("click", function () {
        var imgurl = $(this).data("imgbigurl");
        var bigImg = $(".product__details__pic__item--large").attr("src");
        if (imgurl != bigImg) {
            $(".product__details__pic__item--large").attr({
                src: imgurl,
            });
        }
    });

    /*-------------------
		Quantity change
	--------------------- */
    var proQty = $(".pro-qty");
    proQty.prepend('<span class="dec qtybtn">-</span>');
    proQty.append('<span class="inc qtybtn">+</span>');
    proQty.on("click", ".qtybtn", function () {
        var $button = $(this);
        var oldValue = $button.parent().find("input").val();
        if ($button.hasClass("inc")) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            // Don't allow decrementing below zero
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        $button.parent().find("input").val(newVal).trigger("change"); // thêm trigger("change")
    });

    //new
    $(document).ready(function () {
        let selectedValues = {}; // lưu valueId theo group

        $(".product__details__option input[type=radio]").on(
            "change",
            function () {
                let groupName = $(this).attr("name");
                let valueId = $(this).val();

                selectedValues[groupName] = valueId;

                // Nếu đã chọn đủ tất cả group
                let totalGroups = $(".product__details__option__size").length;
                if (Object.keys(selectedValues).length === totalGroups) {
                    let chosen = Object.values(selectedValues)
                        .map(String)
                        .sort();

                    // Tìm biến thể khớp
                    let matchedIndex = -1;
                    for (let i = 0; i < window.productVariants.length; i++) {
                        let variant = window.productVariants[i];
                        let variantValues = variant.value_id.map(String).sort();

                        if (
                            JSON.stringify(variantValues) ===
                            JSON.stringify(chosen)
                        ) {
                            matchedIndex = i;
                            break;
                        }
                    }

                    if (matchedIndex !== -1) {
                        let price = parseFloat(
                            window.variantPrices[matchedIndex]
                        );
                        let finalPrice = price;

                        if (window.promotions && window.promotions.length > 0) {
                            let promotion = window.promotions[0];
                            if (promotion.discount_type === "percentage") {
                                finalPrice =
                                    price *
                                    (1 -
                                        promotion.discounts[0].discount_value /
                                            100);
                            } else if (promotion.discount_type === "fixed") {
                                finalPrice =
                                    price -
                                    promotion.discounts[0].discount_value;
                            }
                        }

                        // cập nhật giá
                        $("#product-price .product__details__price").text(
                            finalPrice.toLocaleString("vi-VN") + "₫"
                        );
                        if (
                            $("#product-price .product__details__oldprice")
                                .length
                        ) {
                            $(
                                "#product-price .product__details__oldprice"
                            ).text(price.toLocaleString("vi-VN") + "₫");
                        } else {
                            $("#product-price").append(
                                '<span class="product__details__oldprice">' +
                                    price.toLocaleString("vi-VN") +
                                    "₫</span>"
                            );
                        }
                    }
                }

                // Disable những option không hợp lệ
                $(".product__details__option__size").each(function () {
                    let $group = $(this);
                    $group.find("input[type=radio]").each(function () {
                        let testValue = $(this).val();

                        // copy selectedValues tạm
                        let testChosen = Object.assign({}, selectedValues);
                        testChosen[$group.find("input").attr("name")] =
                            testValue;

                        let testArr = Object.values(testChosen)
                            .map(String)
                            .sort();

                        // check có biến thể nào chứa đủ testArr không
                        let valid = window.productVariants.some((v) => {
                            let vArr = v.value_id.map(String).sort();
                            return testArr.every((val) => vArr.includes(val));
                        });

                        $(this).prop("disabled", !valid);
                    });
                });
            }
        );
    });

    //add to cart
    $(".add-to-cart").on("click", function () {
        let productId = $(this).data("product-id");
        let quantity = parseInt($(".pro-qty input").val()) || 1;
        let finalPriceText = $("#product-price .product__details__price")
            .text()
            .replace(/\D/g, "");
        let finalPrice = parseFloat(finalPriceText) || 0;

        let oldPriceText = $("#product-price .product__details__oldprice")
            .text()
            .replace(/\D/g, "");
        let price = oldPriceText ? parseFloat(oldPriceText) : finalPrice;

        let discount = price - finalPrice;

        let selectedValues = [];
        $(".product__details__option input[type=radio]:checked").each(
            function () {
                selectedValues.push($(this).val());
            }
        );

        let productVariantId = null;
        if (
            window.productVariants &&
            selectedValues.length > 0 &&
            selectedValues.length ===
                $(".product__details__option__size").length
        ) {
            let chosen = selectedValues.map(String).sort();
            for (let i = 0; i < window.productVariants.length; i++) {
                let vArr = window.productVariants[i].value_id
                    .map(String)
                    .sort();
                if (JSON.stringify(chosen) === JSON.stringify(vArr)) {
                    productVariantId = window.productVariants[i].id;
                    break;
                }
            }
        }

        $.ajax({
            url: window.Laravel.addToCartUrl,
            type: "POST",
            data: {
                _token: window.Laravel.csrfToken,
                product_id: productId,
                product_variant_id: productVariantId,
                quantity: quantity,
                price: price, // giá gốc
                discount: discount, // giảm giá
                final_price: finalPrice, // giá cuối cùng
            },
            success: function (response) {
                $(".product-details .container .alert").remove();
                if (response.success) {
                    // Cập nhật mini-cart header
                    $(".header__cart ul li a i.fa-shopping-bag + span").text(
                        response.cartCount
                    );
                    $(".header__cart .header__cart__price span").text(
                        response.cartTotal
                    );
                    let alertHtml = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Thêm sản phẩm vào giỏ hàng thành công!</div>`;
                    $(".product-details .container").prepend(alertHtml);
                } else {
                    let alertHtml = `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Có lỗi xảy ra, vui lòng thử lại!</div>`;
                    $(".product-details .container").prepend(alertHtml);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert("Không thể thêm vào giỏ hàng!");
            },
        });
    });

    //update quantity and remove cart_item
    $(document).ready(function () {
        let csrfToken = $('meta[name="csrf-token"]').attr("content");

        $(document).on("change", ".item-quantity", function () {
            let row = $(this).closest("tr");
            let itemId = row.data("item-id");
            let quantity = parseInt($(this).val());

            if (quantity < 1) {
                quantity = 1;
                $(this).val(quantity);
            }

            $.post(
                "/cart/update",
                {
                    _token: csrfToken,
                    item_id: itemId,
                    quantity: quantity,
                },
                function (res) {
                    if (res.success) {
                        row.find(".shoping__cart__total").text(res.item_total);
                        $(".subtotal span").text(res.cart_subtotal);
                        $(".discount span").text("- " + res.cart_discount);
                        $(".total span").text(res.cart_total);
                        $(
                            ".header__cart ul li a i.fa-shopping-bag + span"
                        ).text(res.cartCount);
                        $(".header__cart .header__cart__price span").text(
                            res.cart_total
                        );
                    } else {
                        alert("Cập nhật thất bại!");
                    }
                }
            );
        });

        // remove cart_item
        $(document).on("click", ".remove-item", function () {
            if (!confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?"))
                return;

            let row = $(this).closest("tr");
            let itemId = row.data("item-id");

            $.ajax({
                url: "/cart/remove",
                type: "POST",
                data: {
                    _token: csrfToken,
                    item_id: itemId,
                },
                success: function (res) {
                    if (res.success) {
                        row.remove();
                        $(".subtotal span").text(res.cart_subtotal);
                        $(".discount span").text("- " + res.cart_discount);
                        $(".total span").text(res.cart_total);
                        $(
                            ".header__cart ul li a i.fa-shopping-bag + span"
                        ).text(res.cartCount);
                        $(".header__cart .header__cart__price span").text(
                            res.cart_total
                        );
                    } else {
                        alert("Xóa thất bại!");
                    }
                },
                error: function () {
                    alert("Có lỗi xảy ra, vui lòng thử lại.");
                },
            });
        });
    });

    //checkout
    $(function () {
        let $checkoutBtn = $("#btnCheckout");
        $('input[name="payment_method"]').on("change", function () {
            if ($(this).is(":checked")) {
                $('input[name="payment_method"]')
                    .not(this)
                    .prop("checked", false);
                $checkoutBtn.prop("disabled", false);
            } else {
                if ($('input[name="payment_method"]:checked').length === 0) {
                    $checkoutBtn.prop("disabled", true);
                }
            }
        });
    });

    //submit rating
    $(document).ready(function () {
        $("#product-review-form").submit(function (e) {
            e.preventDefault();

            let form = $(this);
            let productId = form.data("product-id"); // lấy từ data attribute
            let csrfToken = form.data("csrf"); // lấy CSRF token
            let comment = form.find('textarea[name="comment"]').val();
            let rating = form.find('input[name="rating"]:checked').val();
            const perPage = 3;

            $.ajax({
                url: "/product/" + productId + "/rating",
                type: "POST",
                data: {
                    comment: comment,
                    rating: rating,
                    _token: csrfToken,
                },
                success: function (response) {
                    if (response.success) {
                        const reviewList = $("#reviews ul.reviews");

                        // Thêm review mới
                        reviewList.prepend(response.newReviewHtml);

                        // Giữ số review theo perPage
                        while (reviewList.children().length > perPage) {
                            reviewList.children().last().remove();
                        }

                        // Cập nhật toàn bộ #rating từ server
                        $("#rating").replaceWith(response.newRatingHtml);

                        // Reset form
                        form[0].reset();
                        form.find('input[name="rating"][value="1"]').prop(
                            "checked",
                            true
                        );
                    }
                },
                error: function (err) {
                    alert(err.responseJSON.message || "Có lỗi xảy ra");
                },
            });
        });
    });
})(jQuery);
