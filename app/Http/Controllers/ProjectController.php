<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('projects', 'public');
        }

        $project = Project::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'image_path' => $imagePath,
        ]);

        return response()->json($project, 201);
    }

    public function destroy(Project $project)
    {
        if ($project->image_path) {
            Storage::disk('public')->delete($project->image_path);
        }
        
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
