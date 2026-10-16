<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label class="control-label">Danh mục<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" placeholder="Nhập danh mục"
                value="{{ old('name') ?? (isset($categoryOptions) ? $categoryOptions->attribute_category_language->first()?->name ?? '' : '') }}">

        </div>
    </div>
</div>
<div class="row mb15">
    <div class="col-lg-12">
        <div class="form-row">
            <label class="control-label">Mô tả ngắn<span class="text-danger">*</span></label>
            <textarea id="description1" class="form-control summernote" name="description" placeholder="Nhập mô tả ngắn">
                {{ old('description') ?? (isset($categoryOptions) ? $categoryOptions->attribute_category_language->first()?->description ?? '' : '') }}
            </textarea>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200, // chiều cao editor
            tabsize: 2
        });
    });
</script>
