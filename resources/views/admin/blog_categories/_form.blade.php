@csrf
<div class="brush-card p-5 space-y-5 max-w-xl" x-data="{ lang: 'en' }" @invalid.capture="lang = 'en'">
    <x-admin.lang-tabs/>

    <div x-show="lang==='en'" class="space-y-5">
        <x-admin.form-input name="name" label="Name" :value="$blog_category->name" required/>
        <x-admin.form-input name="slug" label="Slug" :value="$blog_category->slug" hint="Leave blank to auto-generate. Shared by both languages."/>
        <x-admin.textarea name="description" label="Description" :value="$blog_category->description"/>
    </div>

    <div x-show="lang==='el'" x-cloak class="space-y-5">
        <x-admin.form-input name="el[name]" label="Name (Ελληνικά)" :value="$blog_category->translation('name')"/>
        <x-admin.textarea name="el[description]" label="Description (Ελληνικά)" :value="$blog_category->translation('description')"/>
    </div>

    <x-admin.form-input name="sort_order" label="Sort order" :value="$blog_category->sort_order ?? 0" type="number"/>
    <x-admin.toggle name="is_active" label="Active" :value="$blog_category->is_active ?? true"/>
</div>
<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-rough is-fire">{{ $blog_category->exists ? 'Update' : 'Create' }}</button>
    <a href="{{ route('admin.blog-categories.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
