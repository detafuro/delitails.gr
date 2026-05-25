<x-admin.layout title="Edit post" :subtitle="$post->title">
    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.posts._form')
    </form>
</x-admin.layout>
