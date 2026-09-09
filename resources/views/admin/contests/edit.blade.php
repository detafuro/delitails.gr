<x-admin.layout title="Edit contest" :subtitle="$contest->title">
    <x-slot:actions>
        <a href="{{ route('admin.contests.entries.index', $contest) }}" class="btn-rough is-bone is-sm">{{ $contest->entries_count }} entries</a>
    </x-slot:actions>
    <form method="POST" action="{{ route('admin.contests.update', $contest) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.contests._form')
    </form>

    {{-- Outside the edit form: the draw posts to its own endpoint. --}}
    <div class="mt-6">
        @include('admin.contests._draw')
    </div>
</x-admin.layout>
