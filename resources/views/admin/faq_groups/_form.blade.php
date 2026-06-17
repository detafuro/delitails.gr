@csrf
<div class="brush-card p-5 space-y-5 max-w-xl">
    <x-admin.form-input name="name" label="Name" :value="$group->name" required/>
    <x-admin.form-input name="slug" label="Slug" :value="$group->slug" hint="Leave blank to auto-generate."/>
    <x-admin.form-input name="sort_order" label="Sort order" :value="$group->sort_order ?? 0" type="number"/>
    <x-admin.toggle name="is_active" label="Active" :value="$group->is_active ?? true"/>
</div>
<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-rough is-fire">{{ $group->exists ? 'Update group' : 'Create group' }}</button>
    <a href="{{ route('admin.faq-groups.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
