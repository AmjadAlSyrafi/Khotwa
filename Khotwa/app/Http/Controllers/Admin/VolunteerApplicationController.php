<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use App\Http\Requests\StoreVolunteerApplicationRequest;
use Illuminate\Http\Request;
use App\Http\Requests\ApproveVolunteerRequest;
use App\Models\{User, Volunteer, VolunteerStatusHistory, Skill, Role};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VolunteerApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendingApplications = VolunteerApplication::where('status', 'pending')->get();

        return response()->json($pendingApplications);
    }

    /**
     * Store a newly created application.
     */
    public function store(StoreVolunteerApplicationRequest $request)
    {
        $application = VolunteerApplication::create($request->validated());

        return response()->json([
            'message' => 'Application has been received successfully.',
            'data' => $application
        ], 201);
    }

    /**
     * Approve a volunteer application and create user and volunteer profiles.
     */
    public function approve(ApproveVolunteerRequest $request)
    {
        DB::beginTransaction();

        try {

            $application = VolunteerApplication::where('id', $request->application_id)
            ->where('status', 'pending')
            ->first();

            if (!$application) {
                return response()->json(['message' => 'Volunteer application not found or not pending.'], 404);
            }

            if ($application->email !== $request->email) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email does not match the application record.',
                ], 422);
            }

            $baseUsername= "user";
            $randomDigits = rand(100, 999);
            $username = $baseUsername . $randomDigits;

            $role = Role::where('name', $request->role)->first();

            if (!$role) {
                return response()->json(['message' => 'Invalid role'], 422);
            }
            // Step 1: Create user account
            $user = User::create([
                'username' => $username,
                'email' => $request->email,
                'password' => Hash::make("12345678"),
                'email_verified_at' => false,
                'password_verified' => false,
                'role_id' => $role->id,
            ]);

            // Step 2: Create volunteer profile
            $volunteer = Volunteer::create([
                'user_id' => $user->id,
                'full_name' => $application->full_name,
                'gender' => $application->gender,
                'birth_date' => $application->date_of_birth,
                'phone' => $application->phone,
                'email' => $application->email,
                'address' => $application->address,
                'city' => $application->city,
                'education_level' => $application->study,
                'university' => $application->career,
                'registration_date' => now(),
                'volunteering_years' => $application->volunteering_years,
                'motivation' => $application->motivation,
                'interests' => $application->interests,
                'availability' => $application->availability,
                'preferred_time' => $application->preferred_time,
                'emergency_contact_name' => $application->emergency_contact_name,
                'emergency_contact_phone' => $application->emergency_contact_phone,
                'emergency_contact_relationship' => $application->emergency_contact_relationship
            ]);

            // Step 3: Attach skills
            if ($application->skills && is_array($application->skills)) {
                $skillIds = [];
                foreach ($application->skills as $skillName) {
                    $skill = Skill::firstOrCreate(['name' => $skillName]);
                    $skillIds[] = $skill->id;
                }
                $volunteer->skills()->sync($skillIds);
            }

            // Step 4: Log initial volunteer status
            VolunteerStatusHistory::create([
                'volunteer_id' => $volunteer->id,
                'status' => 'active',
                'changed_by' => auth()->id(),
            ]);

            // Step 5: Update application status
            $application->status = 'approved';
            $application->save();

            DB::commit();

            return response()->json([
                'message' => 'Volunteer account created successfully.',
                'volunteer_user' => $user,
                'volunteer_profile' => $volunteer,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VolunteerApplication $volunteerApplication)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VolunteerApplication $volunteerApplication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VolunteerApplication $volunteerApplication)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VolunteerApplication $volunteerApplication)
    {
        //
    }
}
