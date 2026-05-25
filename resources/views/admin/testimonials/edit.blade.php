<x-admin.layout title="Edit testimonial">
    <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.testimonials._form')
    </form>
</x-admin.layout>
