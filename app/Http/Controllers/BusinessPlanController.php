<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BusinessPlanController extends Controller
{
    /**
     * Display a listing of active business plans.
     */
    public function index()
    {
        return BusinessPlan::where('is_active', true)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Display a listing of all business plans for admin.
     */
    public function adminIndex()
    {
        return BusinessPlan::orderBy('created_at', 'desc')->get();
    }

    /**
     * Store a newly created business plan in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Healthcare Training,Software Development,Agricultural Technology',
            'file' => 'required|file|mimes:pdf,docx,pptx|max:10240', // 10MB limit
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $path = $file->store('business-plans', 'public');
        
        $size = $this->formatBytes($file->getSize());

        $businessPlan = BusinessPlan::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'file_path' => $path,
            'file_size' => $size,
            'version' => 1,
            'is_active' => true,
        ]);

        return response()->json($businessPlan, 201);
    }

    /**
     * Update the specified business plan.
     */
    public function update(Request $request, $id)
    {
        $businessPlan = BusinessPlan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|in:Healthcare Training,Software Development,Agricultural Technology',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $businessPlan->update($request->only(['title', 'description', 'category', 'is_active']));

        return response()->json($businessPlan);
    }

    /**
     * Remove the specified business plan.
     */
    public function destroy($id)
    {
        $businessPlan = BusinessPlan::findOrFail($id);
        Storage::disk('public')->delete($businessPlan->file_path);
        $businessPlan->delete();

        return response()->json(['message' => 'Business plan deleted successfully']);
    }

    /**
     * Upload a new version of an existing business plan.
     */
    public function uploadNewVersion(Request $request, $id)
    {
        $businessPlan = BusinessPlan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,docx,pptx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Delete old file
        Storage::disk('public')->delete($businessPlan->file_path);

        $file = $request->file('file');
        $path = $file->store('business-plans', 'public');
        $size = $this->formatBytes($file->getSize());

        $businessPlan->update([
            'file_path' => $path,
            'file_size' => $size,
            'version' => $businessPlan->version + 1,
        ]);

        return response()->json($businessPlan);
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
