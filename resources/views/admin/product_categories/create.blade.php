<x-admin.layout title="New category">
    <form method="POST" action="{{ route('admin.product-categories.store') }}" enctype="multipart/form-data">
        @include('admin.product_categories._form')
    </form>
</x-admin.layout>
