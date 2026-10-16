<style>
    .input-wrapper {
        position: relative;
    }

    .baseUrl {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #000000;
    }
</style>

<div class="ibox">
    <div class="ibox-title">
        <h5>Cấu hình SEO</h5>
    </div>
    <div class="ibox-content">
        <div class="seo-container">
            <div class="canonical">
                @php
                    $canonicalValue =
                        old('canonical') ??
                        (isset($categoryOptions)
                            ? $categoryOptions->product_category_language->first()->canonical ?? ''
                            : '');
                @endphp
                {{ 'https://app-ecommerce.test/' . $canonicalValue }}
            </div>
        </div>
        <div class="seo-wrapper">
            <div class="row mb-15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <label class="control-label">Đường dẫn<span class="text-danger">*</span></label>
                        <div class="input-wrapper">
                            <input style="padding-left: 200px;" type="text" class="form-control" name="canonical"
                                placeholder="Nhập đường dẫn"
                                value="{{ old('canonical') ?? (isset($categoryOptions) ? $categoryOptions->product_category_language->first()->canonical ?? '' : '') }}"
                                autocomplete="off">
                            <span class="baseUrl">https://app-ecommerce.test/</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
