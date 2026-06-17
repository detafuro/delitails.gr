@csrf
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 brush-card p-5 space-y-5">
        <x-admin.form-input name="title" label="Title" :value="$post->title" required/>
        <x-admin.form-input name="slug" label="Slug" :value="$post->slug" hint="Leave blank to auto-generate."/>
        <x-admin.textarea name="excerpt" label="Excerpt" :value="$post->excerpt" rows="3"/>
        <x-admin.rich-textarea name="body" label="Body" :value="$post->body" hint="Use formatting tools or paste HTML."/>
        <div class="border-t-2 border-dashed border-ink/30 pt-4">
            <h3 class="font-display text-lg font-extrabold uppercase mb-2">SEO</h3>
            <x-admin.form-input name="seo_title" label="SEO title" :value="$post->seo_title"/>
            <x-admin.textarea class="mt-3" name="seo_description" label="SEO description" :value="$post->seo_description" rows="3"/>
        </div>
    </div>
    <div class="space-y-5">
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Publishing</h3>
            <x-admin.toggle name="is_published" label="Published" :value="$post->is_published ?? true"/>
            <x-admin.form-input name="published_at" label="Publish date" type="datetime-local"
                :value="$post->published_at ? $post->published_at->format('Y-m-d\TH:i') : ''"/>
            <x-admin.select name="category_id" label="Category" :value="$post->category_id" placeholder="— None —"
                :options="$categories->pluck('name','id')->toArray()"/>
            <x-admin.form-input name="author" label="Author" :value="$post->author"/>
            <x-admin.form-input name="tags" label="Tags" :value="is_array($post->tags) ? implode(', ', $post->tags) : ''" hint="Comma-separated."/>
        </div>
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Featured image</h3>
            <x-admin.image-upload name="featured_image" :currentPath="$post->featured_image"/>
        </div>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-rough is-fire">{{ $post->exists ? 'Update post' : 'Create post' }}</button>
    <a href="{{ route('admin.posts.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
