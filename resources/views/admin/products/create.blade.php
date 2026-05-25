<x-admin.layout title="New product">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @include('admin.products._form')
    </form>
</x-admin.layout>
