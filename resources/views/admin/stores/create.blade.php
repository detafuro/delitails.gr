<x-admin.layout title="New store">
    <form method="POST" action="{{ route('admin.stores.store') }}">@include('admin.stores._form')</form>
</x-admin.layout>
