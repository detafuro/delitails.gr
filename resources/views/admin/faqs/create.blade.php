<x-admin.layout title="New FAQ">
    <form method="POST" action="{{ route('admin.faqs.store') }}">
        @include('admin.faqs._form')
    </form>
</x-admin.layout>
