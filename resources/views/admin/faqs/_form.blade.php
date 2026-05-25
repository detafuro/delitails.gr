@csrf
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 brush-card p-5 space-y-5">
        <x-admin.form-input name="question" label="Question" :value="$faq->question" required/>
        <x-admin.textarea name="answer" label="Answer" :value="$faq->answer" rows="8" required/>

        <div class="border-t-2 border-dashed border-ink/30 pt-4">
            <h3 class="font-display text-lg font-extrabold uppercase mb-2">SEO</h3>
            <x-admin.form-input name="seo_title" label="SEO title" :value="$faq->seo_title"/>
            <x-admin.textarea class="mt-3" name="seo_description" label="SEO description" :value="$faq->seo_description" rows="3"/>
        </div>
    </div>
    <div class="space-y-5">
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Placement</h3>
            <x-admin.select name="group_id" label="Group" :value="$faq->group_id" placeholder="— Ungrouped —"
                :options="$groups->pluck('name','id')->toArray()"/>
            <x-admin.form-input name="sort_order" label="Sort order" :value="$faq->sort_order ?? 0" type="number"/>
            <x-admin.toggle name="is_active" label="Active" :value="$faq->is_active ?? true"/>
            <x-admin.toggle name="show_on_homepage" label="Show on homepage" :value="$faq->show_on_homepage"/>
        </div>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button class="btn-rough is-fire">{{ $faq->exists ? 'Update FAQ' : 'Create FAQ' }}</button>
    <a href="{{ route('admin.faqs.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
