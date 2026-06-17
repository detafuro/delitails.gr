@csrf
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 brush-card p-5 space-y-5">
        <x-admin.form-input name="name" label="Name" :value="$category->name" required/>
        <x-admin.form-input name="slug" label="Slug" :value="$category->slug" hint="Leave blank to auto-generate."/>
        <x-admin.textarea name="description" label="Description" :value="$category->description" rows="4"/>

        <div class="border-t-2 border-dashed border-ink/30 pt-4">
            <h3 class="font-display text-lg font-extrabold uppercase mb-2">SEO</h3>
            <x-admin.form-input name="seo_title" label="SEO title" :value="$category->seo_title"/>
            <x-admin.textarea class="mt-3" name="seo_description" label="SEO description" :value="$category->seo_description" rows="3"/>
        </div>
    </div>

    <div class="space-y-5">
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Settings</h3>
            <x-admin.toggle name="is_active" label="Active" :value="$category->is_active ?? true"/>
            <x-admin.form-input name="sort_order" label="Sort order" :value="$category->sort_order ?? 0" type="number"/>
            <x-admin.select name="parent_id" label="Parent category" :value="$category->parent_id" placeholder="— None —"
                :options="$parents->pluck('name','id')->toArray()"/>
        </div>
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Image</h3>
            <x-admin.image-upload name="image" :currentPath="$category->image"/>
        </div>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-rough is-fire">{{ $category->exists ? 'Update category' : 'Create category' }}</button>
    <a href="{{ route('admin.product-categories.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
