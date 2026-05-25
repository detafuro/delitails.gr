<x-admin.layout title="New post">
    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
        @include('admin.posts._form')
    </form>
</x-admin.layout>
