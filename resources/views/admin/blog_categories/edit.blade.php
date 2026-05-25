<x-admin.layout title="Edit blog category" :subtitle="$blog_category->name">
    <form method="POST" action="{{ route('admin.blog-categories.update', $blog_category) }}">
        @method('PUT')
        @include('admin.blog_categories._form')
    </form>
</x-admin.layout>
