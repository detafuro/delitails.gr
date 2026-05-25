<x-admin.layout title="Edit FAQ group" :subtitle="$group->name">
    <form method="POST" action="{{ route('admin.faq-groups.update', $group) }}">
        @method('PUT')
        @include('admin.faq_groups._form')
    </form>
</x-admin.layout>
