@csrf
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5 brush-card p-5">
        <x-admin.form-input name="title" label="Title" :value="$product->title" required/>
        <x-admin.form-input name="slug" label="Slug" :value="$product->slug" hint="Leave blank to auto-generate."/>
        <x-admin.textarea name="short_description" label="Short description" :value="$product->short_description" rows="2"/>
        <x-admin.textarea name="description" label="Full description" :value="$product->description" rows="10"/>
        <x-admin.textarea name="characteristics" label="Characteristics (HTML)" :value="$product->characteristics" rows="6"
            hint="Raw HTML (typically a &lt;ul&gt; of bullet points). Rendered as-is on the product page."/>

        <x-admin.form-input name="sku" label="SKU" :value="$product->sku"/>

        <div class="border-t-2 border-dashed border-ink/30 pt-4">
            <h3 class="font-display text-lg font-extrabold uppercase mb-2">SEO</h3>
            <x-admin.form-input name="seo_title" label="SEO title" :value="$product->seo_title"/>
            <x-admin.textarea class="mt-3" name="seo_description" label="SEO description" :value="$product->seo_description" rows="3"/>
        </div>
    </div>

    <div class="space-y-5">
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Status</h3>
            <x-admin.toggle name="is_published" label="Published" :value="$product->is_published ?? true"/>
            <x-admin.toggle name="is_featured" label="Featured" :value="$product->is_featured"/>
            <x-admin.select name="stock_status" label="Stock status" :value="$product->stock_status ?? 'in_stock'" :options="[
                'in_stock' => 'In stock', 'out_of_stock' => 'Out of stock', 'preorder' => 'Preorder',
            ]"/>
            <x-admin.form-input name="sort_order" label="Sort order" :value="$product->sort_order ?? 0" type="number"/>
        </div>

        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Classification</h3>
            <x-admin.select name="category_id" label="Animal" :value="$product->category_id" placeholder="— Uncategorised —" :options="$categories->pluck('name','id')->toArray()"/>
            <x-admin.select name="type" label="Type" :value="$product->type" placeholder="— None —" :options="\App\Models\Product::TYPES"/>
        </div>

        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Featured image</h3>
            <x-admin.image-upload name="featured_image" :currentPath="$product->featured_image"
                :deleteUrl="null" hint="JPG / PNG / WebP, up to 5MB."/>
        </div>

        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Gallery</h3>
            @if($product->exists && $product->images->count())
                <div class="grid grid-cols-3 gap-2 mb-3">
                    @foreach($product->images as $img)
                        <div class="relative group border border-ink/30">
                            <img src="{{ asset('storage/'.$img->path) }}" class="h-24 w-full object-cover">
                            <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $img]) }}" class="absolute inset-0 hidden group-hover:flex items-center justify-center bg-ink/70">
                                @csrf @method('DELETE')
                                <button class="text-bone text-xs font-bold uppercase">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
            <x-admin.image-upload name="gallery" :multiple="true" hint="Add additional images."/>
        </div>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-rough is-fire" onclick="console.log('Button clicked'); console.log('Form:', this.form); if(this.form) { console.log('Submitting...'); this.form.submit(); }">{{ $product->exists ? 'Update product' : 'Create product' }}</button>
    <a href="{{ route('admin.products.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
