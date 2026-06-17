@csrf
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 brush-card p-5 space-y-5">
        <x-admin.form-input name="name" label="Store name" :value="$store->name" required/>
        <x-admin.form-input name="slug" label="Slug" :value="$store->slug" hint="Leave blank to auto-generate."/>
        <div class="grid sm:grid-cols-2 gap-4">
            <x-admin.form-input name="city" label="City" :value="$store->city" required/>
            <x-admin.form-input name="postcode" label="Postcode" :value="$store->postcode"/>
        </div>
        <x-admin.form-input name="address" label="Address" :value="$store->address" required/>
        <div class="grid sm:grid-cols-2 gap-4">
            <x-admin.form-input name="phone" label="Phone" :value="$store->phone"/>
            <x-admin.form-input name="email" label="Email" type="email" :value="$store->email"/>
        </div>
        <x-admin.textarea name="opening_hours" label="Opening hours" :value="$store->opening_hours" rows="5"
            hint="One line per day, eg. 'Mon–Fri: 09:00 – 18:00'."/>
        <x-admin.form-input name="map_link" label="Map link (URL)" :value="$store->map_link" type="url"/>
        <div class="grid sm:grid-cols-2 gap-4">
            <x-admin.form-input name="latitude" label="Latitude" :value="$store->latitude" type="number" step="any"/>
            <x-admin.form-input name="longitude" label="Longitude" :value="$store->longitude" type="number" step="any"/>
        </div>
    </div>
    <div class="space-y-5">
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Visibility</h3>
            <x-admin.toggle name="is_active" label="Active" :value="$store->is_active ?? true"/>
            <x-admin.form-input name="sort_order" label="Sort order" :value="$store->sort_order ?? 0" type="number"/>
        </div>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-rough is-fire">{{ $store->exists ? 'Update store' : 'Create store' }}</button>
    <a href="{{ route('admin.stores.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
