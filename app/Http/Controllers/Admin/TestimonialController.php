<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends AdminController
{
    public function index()
    {
        $this->authorize('viewAny', Testimonial::class);
        $testimonials = Testimonial::orderBy('sort_order')->latest()->paginate(15);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        $this->authorize('create', Testimonial::class);
        $testimonial = new Testimonial();
        return view('admin.testimonials.create', compact('testimonial'));
    }

    public function store(StoreTestimonialRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }
        Testimonial::create($data);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created.');
    }

    public function edit(Testimonial $testimonial)
    {
        $this->authorize('update', $testimonial);
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(StoreTestimonialRequest $request, Testimonial $testimonial)
    {
        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar) Storage::disk('public')->delete($testimonial->avatar);
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }
        $testimonial->update($data);
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->authorize('delete', $testimonial);
        if ($testimonial->avatar) Storage::disk('public')->delete($testimonial->avatar);
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted.');
    }
}
