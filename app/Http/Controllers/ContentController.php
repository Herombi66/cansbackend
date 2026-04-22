<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function index()
    {
        return Content::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('contents', 'public');
        }

        $content = Content::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'image_path' => $path,
            'user_id' => $request->user() ? $request->user()->id : null,
        ]);

        return response()->json($content, 201);
    }

    public function destroy(Content $content)
    {
        if ($content->image_path) {
            Storage::disk('public')->delete($content->image_path);
        }
        $content->delete();
        return response()->json(['message' => 'Content deleted']);
    }
}