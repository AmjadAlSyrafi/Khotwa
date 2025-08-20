<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ConfirmDonationRequest;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\InitDonationRequest;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class DonationController extends Controller
{
    //============================================
    // ADMIN-FACING CRUD OPERATIONS
    //============================================

    /**
     * Admin - List all donations.
     */
    public function index()
    {
        $donations = Donation::with(['project', 'event'])->latest()->get();
        return ApiResponse::success(DonationResource::collection($donations), 'Donations fetched successfully.');
    }

    /**
     * Admin - Store a new manual donation.
     * For example: cash or bank transfer entered manually by admin.
     */
    public function store(StoreDonationRequest $request)
    {
        $data = $request->validated();

        // Default behavior for manual entry
        $data['payment_status'] = $data['payment_status'] ?? 'paid';
        $data['method'] = $data['method'] ?? 'manual';
        $data['donated_at'] = now();

        $donation = Donation::create($data);

        return ApiResponse::success(new DonationResource($donation), 'Manual donation created successfully.', 201);
    }

    /**
     * Admin - Show a single donation.
     */
    public function show($id)
    {
        $donation = Donation::with(['project', 'event'])->find($id);
        if (!$donation) {
            return ApiResponse::error('Donation not found.', 404);
        }
        return ApiResponse::success(new DonationResource($donation));
    }

    /**
     * Admin - Update a donation.
     */
    public function update(StoreDonationRequest $request, $id)
    {
        $donation = Donation::find($id);
        if (!$donation) {
            return ApiResponse::error('Donation not found.', 404);
        }

        $data = $request->validated();
        $donation->update($data);

        return ApiResponse::success(new DonationResource($donation), "Donation updated successfully.");
    }

    /**
     * Admin - Delete a donation.
     */
    public function destroy($id)
    {
        $donation = Donation::find($id);
        if (!$donation) {
            return ApiResponse::error('Donation not found.', 404);
        }

        $donation->delete();
        return ApiResponse::success(null, 'Donation deleted successfully.');
    }


    /**
     * Admin - Get donation statistics (overall summary).
     */
    public function statistics()
    {
        $totalAmount = Donation::where('payment_status', 'paid')->sum('amount');
        $totalDonations = Donation::where('payment_status', 'paid')->count();

        $byProjects = Donation::selectRaw('project_id, SUM(amount) as total_amount, COUNT(*) as total_donations')
            ->where('payment_status', 'paid')
            ->groupBy('project_id')
            ->with('project:id,name')
            ->get();

        $byEvents = Donation::selectRaw('event_id, SUM(amount) as total_amount, COUNT(*) as total_donations')
            ->where('payment_status', 'paid')
            ->groupBy('event_id')
            ->with('event:id,title')
            ->get();

        return ApiResponse::success([
            'total_amount'   => $totalAmount,
            'total_donations'=> $totalDonations,
            'by_projects'    => $byProjects,
            'by_events'      => $byEvents,
        ], 'Donation statistics fetched successfully.');
    }

    //============================================
    // MOBILE APP PAYMENT FLOW
    //============================================

    /**
     * Mobile - Step 1: Initialize a new donation with 'pending' status.
     */
    public function init(InitDonationRequest $request)
    {
        $validatedData = $request->validated();

        $donation = Donation::create([
            'amount'         => $validatedData['amount'],
            'project_id'     => $validatedData['project_id'] ?? null,
            'event_id'       => $validatedData['event_id'] ?? null,
            'donor_name'     => $validatedData['donor_name'] ?? null,
            'donor_email'    => $validatedData['donor_email'] ?? null,
            'type'           => $validatedData['type'],
            'payment_status' => 'pending',
            'method'         => $validatedData['method'] ?? 'mobile',
        ]);

        return ApiResponse::success([
            'donation_id' => $donation->id,
            'status'      => $donation->payment_status
        ], 'Donation initialized successfully.');
    }

    /**
     * Mobile - Step 2: Confirm a donation after payment success/failure.
     */
    public function confirm(ConfirmDonationRequest $request)
    {
        $validatedData = $request->validated();

        $donation = Donation::find($validatedData['donation_id']);
        if (!$donation) {
            return ApiResponse::error('Donation not found.', 404);
        }

        // Only allow confirm if still pending
        if ($donation->payment_status !== 'pending') {
            return ApiResponse::error('Donation already processed.', 400);
        }

        // Integrity check: Match the amount
        if ((float) $donation->amount !== (float) $validatedData['amount']) {
            Log::error("Donation amount mismatch on confirm.", [
                'donation_id'     => $donation->id,
                'original_amount' => $donation->amount,
                'confirm_amount'  => $validatedData['amount']
            ]);
            return ApiResponse::error('Donation amount mismatch.', 400);
        }

        $donation->update([
            'transaction_id' => $validatedData['transaction_id'] ?? null,
            'payment_status' => $validatedData['payment_status'],
            'method'         => $validatedData['method'] ?? $donation->method,
            'donated_at'     => now(),
        ]);

        return ApiResponse::success([
            'donation_id' => $donation->id,
            'status'      => $donation->payment_status
        ], 'Donation status confirmed successfully.');
    }

    //============================================
    // USER ENDPOINT
    //============================================

    /**
     * Authenticated User - List my donations (based on donor_email).
    */
    public function myDonations()
    {
        $user = Auth::user();

        if (!$user || !$user->email) {
            return ApiResponse::error('User email not found. Cannot fetch donations.', 400);
        }

        $donations = Donation::where('donor_email', $user->email)
            ->with(['project', 'event'])
            ->latest()
            ->get();

        return ApiResponse::success(DonationResource::collection($donations), 'Your donations fetched successfully.');
    }

    //============================================
    // PUBLIC ENDPOINTS
    //============================================

    /**
     * Public - List donations by project.
    */
    public function listByProject($projectId)
    {
        $donations = Donation::where('project_id', $projectId)
            ->where('payment_status', 'paid')
            ->with(['project', 'event'])
            ->latest()
            ->get();

        return ApiResponse::success(DonationResource::collection($donations), 'Project donations fetched successfully.');
    }

    /**
     * Public - List donations by event.
    */
    public function listByEvent($eventId)
    {
        $donations = Donation::where('event_id', $eventId)
            ->where('payment_status', 'paid')
            ->with(['project', 'event'])
            ->latest()
            ->get();

        return ApiResponse::success(DonationResource::collection($donations), 'Event donations fetched successfully.');
    }

}
