<x-admin.layout title="New contest">
    <form method="POST" action="{{ route('admin.contests.store') }}" enctype="multipart/form-data">
        @include('admin.contests._form')
    </form>
</x-admin.layout>
