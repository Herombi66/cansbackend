<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Mail\MeetingConfirmationMail;
use App\Mail\MeetingStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /**
     * Store a new meeting request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'company' => 'required|string|max:255',
            'meeting_type' => 'required|string|max:100',
            'duration' => 'required|integer|min:15|max:120',
            'scheduled_at' => 'required|date|after:now',
            'timezone' => 'required|string|max:100',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check availability (simple check for now)
        $scheduledAt = Carbon::parse($request->scheduled_at);
        $endTime = $scheduledAt->copy()->addMinutes($request->duration);

        $exists = Meeting::where('status', 'confirmed')
            ->where(function ($query) use ($scheduledAt, $endTime) {
                $query->whereBetween('scheduled_at', [$scheduledAt, $endTime])
                    ->orWhereRaw('? BETWEEN scheduled_at AND DATE_ADD(scheduled_at, INTERVAL duration MINUTE)', [$scheduledAt]);
            })->exists();

        if ($exists) {
            return response()->json(['message' => 'The selected time slot is already booked.'], 409);
        }

        $meeting = Meeting::create([
            'tracking_id' => 'MTG-' . strtoupper(Str::random(10)),
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'company' => $request->company,
            'meeting_type' => $request->meeting_type,
            'duration' => $request->duration,
            'scheduled_at' => $scheduledAt,
            'timezone' => $request->timezone,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Send automated email confirmation
        try {
            Mail::to($meeting->email)->send(new MeetingConfirmationMail($meeting));
        } catch (\Exception $e) {
            \Log::error('Failed to send meeting confirmation email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Meeting request submitted successfully.',
            'meeting' => $meeting
        ], 201);
    }

    /**
     * Display a listing of the meetings for admin.
     */
    public function index()
    {
        $meetings = Meeting::orderBy('scheduled_at', 'desc')->get();
        return response()->json($meetings);
    }

    /**
     * Update the status of a meeting.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,rejected,completed',
            'internal_notes' => 'nullable|string',
        ]);

        $meeting = Meeting::findOrFail($id);
        $meeting->update([
            'status' => $request->status,
            'internal_notes' => $request->internal_notes,
        ]);

        // Send automated notification to investor
        try {
            Mail::to($meeting->email)->send(new MeetingStatusMail($meeting));
        } catch (\Exception $e) {
            \Log::error('Failed to send meeting status email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Meeting status updated successfully.',
            'meeting' => $meeting
        ]);
    }

    /**
     * Delete a meeting request.
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();

        return response()->json(['message' => 'Meeting request deleted successfully.']);
    }

    /**
     * Check availability for a given date.
     */
    public function checkAvailability(Request $request)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json(['message' => 'Date is required.'], 400);
        }

        $dayStart = Carbon::parse($date)->startOfDay();
        $dayEnd = Carbon::parse($date)->endOfDay();

        $bookedSlots = Meeting::where('status', 'confirmed')
            ->whereBetween('scheduled_at', [$dayStart, $dayEnd])
            ->select('scheduled_at', 'duration')
            ->get();

        return response()->json($bookedSlots);
    }
}
