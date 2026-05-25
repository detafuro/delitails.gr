<x-admin.layout title="New testimonial">
    <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data">
        @include('admin.testimonials._form')
    </form>
</x-admin.layout>
