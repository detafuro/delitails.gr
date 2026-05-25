<x-admin.layout title="Edit FAQ">
    <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
        @method('PUT')
        @include('admin.faqs._form')
    </form>
</x-admin.layout>
