@csrf
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 brush-card p-5 space-y-5">
        <div class="grid sm:grid-cols-2 gap-4">
            <x-admin.form-input name="author" label="Author" :value="$testimonial->author" required/>
            <x-admin.form-input name="pet_name" label="Pet name" :value="$testimonial->pet_name"/>
        </div>
        <x-admin.textarea name="quote" label="Quote" :value="$testimonial->quote" rows="5" required/>
        <x-admin.select name="rating" label="Rating" :value="$testimonial->rating ?? 5"
            :options="[5=>'5 stars',4=>'4 stars',3=>'3 stars',2=>'2 stars',1=>'1 star']"/>
    </div>
    <div class="space-y-5">
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Avatar</h3>
            <x-admin.image-upload name="avatar" :currentPath="$testimonial->avatar"/>
        </div>
        <div class="brush-card p-5 space-y-4">
            <x-admin.toggle name="is_active" label="Active" :value="$testimonial->is_active ?? true"/>
            <x-admin.form-input name="sort_order" label="Sort order" :value="$testimonial->sort_order ?? 0" type="number"/>
        </div>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-rough is-fire">{{ $testimonial->exists ? 'Update' : 'Create' }}</button>
    <a href="{{ route('admin.testimonials.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
