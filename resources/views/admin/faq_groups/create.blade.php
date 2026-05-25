<x-admin.layout title="New FAQ group">
    <form method="POST" action="{{ route('admin.faq-groups.store') }}">@include('admin.faq_groups._form')</form>
</x-admin.layout>
