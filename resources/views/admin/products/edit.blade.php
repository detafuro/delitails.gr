<x-admin.layout title="Edit product" :subtitle="$product->title">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.products._form')
    </form>
</x-admin.layout>
