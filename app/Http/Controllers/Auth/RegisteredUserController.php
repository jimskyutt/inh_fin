<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Barangay; 

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $barangays = Barangay::orderBy('brgy_name')->get();
        return view('auth.register', compact('barangays'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        try {
            Log::info('Starting user registration', ['email' => $request->email]);
            
            // Log request data (excluding sensitive information)
            $loggableRequest = $request->except(['password', 'password_confirmation', 'face_image']);
            Log::debug('Registration request data', $loggableRequest);

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'in:Homeowner,ServiceProvider'],
                'civil_status' => ['required', 'string', 'in:single,married,divorced,widowed'],
                'birthday' => ['required', 'date', 'before:today'],
                'age' => ['required', 'integer', 'min:18'],
                'contact_number' => [
                    'required', 
                    'string', 
                    'regex:/^09\d{9}$/',
                    'max:11',
                    'min:11'
                ],
                'sex' => ['required', 'string', 'in:male,female,other'],
                'address' => ['required', 'string', 'max:255'],
                'street' => ['required', 'string', 'max:255'],
                'face_image' => ['required', 'string', 'starts_with:data:image/jpeg;base64,'],
                'police_clearance' => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
                'id_front' => ['required', 'file', 'mimes:jpeg,png,jpg', 'max:2048'],
                'id_back' => ['required', 'file', 'mimes:jpeg,png,jpg', 'max:2048'],
                'services' => ['required_if:role,ServiceProvider', 'array'],
                'services.*' => ['exists:services,service_id'],
            ], [
                'contact_number.regex' => 'The contact number must start with 09 and be 11 digits long.',
                'contact_number.min' => 'The contact number must be exactly 11 digits long.',
                'contact_number.max' => 'The contact number must be exactly 11 digits long.'
            ]);
            
            Log::info('Validation passed', ['email' => $request->email]);

            // Log file uploads
            Log::debug('Processing file uploads', [
                'has_face_image' => !empty($request->face_image),
                'has_police_clearance' => $request->hasFile('police_clearance'),
                'has_id_front' => $request->hasFile('id_front'),
                'has_id_back' => $request->hasFile('id_back')
            ]);

            // Handle file uploads
            $faceImageName = null;
            if (str_starts_with($request->face_image, 'data:image/jpeg;base64,')) {
                $faceImageName = $this->saveBase64Image($request->face_image, 'face_images');
                Log::info('Face image processed', ['filename' => $faceImageName]);
            } else {
                $faceImageName = $this->uploadFile($request->file('face_image'), 'face_images');
                Log::info('Face image uploaded', ['filename' => $faceImageName]);
            }
            
            $policeClearancePath = $this->uploadFile($request->file('police_clearance'), 'police_clearances');
            $idFrontPath = $this->uploadFile($request->file('id_front'), 'id_cards');
            $idBackPath = $this->uploadFile($request->file('id_back'), 'id_cards');
            
            Log::debug('Files uploaded', [
                'police_clearance' => $policeClearancePath,
                'id_front' => $idFrontPath,
                'id_back' => $idBackPath
            ]);

            // Create user (email not verified yet)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'civil_status' => $request->civil_status,
                'birthday' => $request->birthday,
                'age' => $request->age,
                'contact_number' => $request->contact_number,
                'sex' => $request->sex,
                'address' => $request->address,
                'street' => $request->street,
                'face_img' => $faceImageName,
                'police_clearance' => $policeClearancePath,
                'id_front' => $idFrontPath,
                'id_back' => $idBackPath,
                'status' => 'pending',
                'email_verified_at' => null, // Explicitly set to null
            ]);
            
            // Log in the user
            Auth::login($user);
            
            try {
                // Send email verification notification
                $user->sendEmailVerificationNotification();
                Log::info('Verification email sent to new user', ['user_id' => $user->id, 'email' => $user->email]);
            } catch (\Exception $e) {
                Log::error('Failed to send verification email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Continue with registration even if email sending fails
                // The user can request a new verification email later
            }

            Log::debug('Creating user with data', array_merge(
                array_diff_key([
                    'name' => $request->name,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => '[HIDDEN]',
                    'role' => $request->role,
                    'civil_status' => $request->civil_status,
                    'birthday' => $request->birthday,
                    'age' => $request->age,
                    'contact_number' => $request->contact_number,
                    'sex' => $request->sex,
                    'address' => $request->address,
                    'street' => $request->street,
                    'face_img' => $faceImageName,
                    'police_clearance' => $policeClearancePath,
                    'id_front' => $idFrontPath,
                    'id_back' => $idBackPath,
                ], ['password' => '']), // Exclude password from logs
                ['password' => '[HIDDEN]']
            ));

            Log::info('User created', ['user_id' => $user->id, 'email' => $user->email]);

            // Attach services if user is a service provider
            if ($request->role === 'ServiceProvider' && $request->has('services')) {
                $user->services()->attach($request->services);
                Log::info('Services attached', [
                    'user_id' => $user->id,
                    'services' => $request->services
                ]);
            }

            // Log successful registration
            Log::info('User registration completed successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            if ($request->wantsJson()) {
                return response()->json(['user' => $user]);
            }

            return redirect()->route('verification.notice');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed during registration', [
                'errors' => $e->errors(),
                'input' => $request->except(['password', 'password_confirmation', 'face_image'])
            ]);
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except(['password', 'password_confirmation', 'face_image'])
            ]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Registration failed. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            
            return back()->withInput()->withErrors([
                'registration' => 'Registration failed. Please try again.'
            ]);
        }
    }

    private function uploadFile($file, $directory)
{
    $fileName = $directory . '/' . uniqid() . '.' . $file->getClientOriginalExtension();
    Storage::disk('public')->put($fileName, file_get_contents($file));
    return $fileName;
}

private function saveBase64Image($base64Image, $directory)
{
    $image = str_replace('data:image/jpeg;base64,', '', $base64Image);
    $image = str_replace(' ', '+', $image);
    $imageName = $directory . '/' . uniqid() . '.jpg';
    Storage::disk('public')->put($imageName, base64_decode($image));
    return $imageName;
}

    public function checkUnique(Request $request)
    {
        try {
            $field = $request->input('field');
            $value = $request->input('value');
            
            \Log::info('Checking uniqueness', ['field' => $field, 'value' => $value]);
            
            // Map field names to their display names
            $fieldDisplayNames = [
                'email' => 'Email',
                'contact_number' => 'Contact Number',
                'username' => 'Username'
            ];
    
            // Validate the field exists in the allowed fields
            $allowedFields = array_keys($fieldDisplayNames);
            if (!in_array($field, $allowedFields)) {
                \Log::error('Invalid field provided', ['field' => $field]);
                return response()->json([
                    'exists' => false,
                    'message' => 'Invalid field'
                ], 400);
            }
            
            $exists = \App\Models\User::where($field, $value)->exists();
            $displayName = $fieldDisplayNames[$field] ?? $field;
            
            \Log::info('Uniqueness check result', ['field' => $field, 'value' => $value, 'exists' => $exists]);
            
            return response()->json([
                'exists' => $exists,
                'message' => $exists ? "$displayName is already registered" : ""
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in checkUnique: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'An error occurred while checking uniqueness',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
