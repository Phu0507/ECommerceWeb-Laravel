<style>
    .ibox-title h5 {
        float: none;
    }

    .variant-checkbox input {
        margin-top: 0;
        width: 16px;
        height: 16px;
    }

    .variant-checkbox label {
        margin-bottom: 0;
        margin-left: 10px;
        font-weight: normal;
        cursor: pointer;
    }

    .variant-container {
        margin-top: 30px;
    }

    .attribute-title {
        font-size: 12px;
        color: #1791f2;
    }

    .variant-body {
        margin-top: 10px;
    }

    .add-variant {
        height: 40px;
        color: #1791f2;
        border: 1px dashed #1791f2;
        background: #fafafa;
        text-align: center;
        border-radius: 5px;
        transition: all 0.3 ease;
        padding: 0 40px;
    }

    /* Select to hơn */
    .choose-attribute {
        height: 40px;
        font-size: 13px;
        border-radius: 5px;
        padding: 5px 10px;
    }

    .select2-container--default .select2-selection--multiple {
        height: 40px;
    }
</style>

<div class="ibox variant-box">
    <div class="ibox-title">
        <h5>Sản phẩm có nhiều phiên bản</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="variant-checkbox uk-flex uk-flex-middle" style="display:flex; align-items:center; ">
                    <input type="checkbox" value="1" name="accecpt" id="variantCheckbox" style="margin:0;"
                        @if (!empty($categoryOptions['variant_sku'])) checked @endif>
                    <label for="variantCheckbox" class="turnOnVariant">
                        Sản phẩm này có nhiều biến thể. Ví dụ: màu sắc, kích thước
                    </label>

                </div>

            </div>
        </div>
        <div class="variant-wrapper {{ !empty($categoryOptions['variant_sku']) ? '' : 'hidden' }}">
            <div class="row variant-container">
                <div class="col-lg-3">
                    <div class="attribute-title">
                        <h4>Chọn thuộc tính</h4>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="attribute-title">
                        <h4>Chọn giá trị thuộc tính</h4>
                    </div>
                </div>
            </div>
            <div class="variant-body">
            </div>
            <div class="variant-foot mt10">
                <button type="button" class="add-variant">Thêm thuộc tính</button>
            </div>
        </div>
    </div>
</div>

<div class="ibox product-variant">
    <div class="ibox-title">
        <h5>Danh sách phiên bản</h5>
    </div>
    <div class="ibox-content">
        <div class="table-responsive">
            <table class="table table-striped variantTable">
                <thead style="background-color: #333; color: #fff">
                    <tr>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var attributeCategory = @json($attributeCategory);
    var variantData = @json($categoryOptions['variant_attributes'] ?? []);
    var variantSku = @json($categoryOptions['variant_sku'] ?? []);
    var variantQuantity = @json($categoryOptions['variant_quantity'] ?? []);
    var variantPrice = @json($categoryOptions['variant_price'] ?? []);
    var variantImage = @json($categoryOptions['variant_image'] ?? []);
</script>
