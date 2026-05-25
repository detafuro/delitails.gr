<x-admin.layout title="New blog category">
    <form method="POST" action="{{ route('admin.blog-categories.store') }}">
        @include('admin.blog_categories._form')
    </form>
</x-admin.layout>
