<x-admin.layout title="Edit store" :subtitle="$store->name">
    <form method="POST" action="{{ route('admin.stores.update', $store) }}">
        @method('PUT')
        @include('admin.stores._form')
    </form>
</x-admin.layout>
