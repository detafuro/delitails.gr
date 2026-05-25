<x-admin.layout title="Edit category" :subtitle="$category->name">
    <form method="POST" action="{{ route('admin.product-categories.update', $category) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.product_categories._form')
    </form>
</x-admin.layout>
