<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $userId = $user ? $user->id : null;
        $isAdmin = $user && $user->role === 'Admin';
        $routeUser = $this->route('user');
        
        // If admin is editing another user, get that user's ID for validation
        $targetUserId = $isAdmin && $routeUser ? $routeUser->id : $userId;
        
        // Get the current user's email and contact number for comparison
        $currentUser = $targetUserId ? \App\Models\User::find($targetUserId) : null;
        $currentEmail = $currentUser ? $currentUser->email : null;
        $currentContact = $currentUser ? $currentUser->contact_number : null;
        
        // Email rule - skip unique check if email hasn't changed
        $emailRules = [
            'required',
            'string',
            'email',
            'max:255',
        ];
        
        // Only add unique rule if email is being changed
        if ($currentEmail !== $this->input('email')) {
            $emailRules[] = Rule::unique('users')->ignore($targetUserId);
        }
        
        // Contact number rule - skip unique check if contact number hasn't changed
        $contactRules = [
            'required',
            'string',
            'regex:/^\+?[0-9]{10,15}$/'
        ];
        
        // Only add unique rule if contact number is being changed
        if ($currentContact !== $this->input('contact_number')) {
            $contactRules[] = 'unique:users,contact_number';
        }
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRules,
            'contact_number' => $contactRules,
            'birthday' => ['required', 'date', 'before:-18 years'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'sex' => ['required', 'string', 'in:male,female,other'],
            'civil_status' => ['required', 'string', 'in:single,married,divorced,widowed'],
            'address' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'profile_photo' => [
                'nullable',
                'file',
                'image',
                'max:5120', // 5MB
                'mimes:jpeg,png,jpg,gif,webp'
            ],
            'id_front' => [
                'nullable',
                'file',
                'image',
                'max:5120',
                'mimes:jpeg,png,jpg,gif,webp'
            ],
            'id_back' => [
                'nullable',
                'file',
                'image',
                'max:5120',
                'mimes:jpeg,png,jpg,gif,webp'
            ],
            'police_clearance' => [
                'nullable',
                'file',
                'image',
                'max:5120',
                'mimes:jpeg,png,jpg,gif,webp'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'contact_number.regex' => 'The contact number format is invalid. It should start with + and contain 10-15 digits.',
            'profile_photo.image' => 'The file must be an image (jpeg, png, bmp, gif, svg, or webp).',
            'profile_photo.max' => 'The profile photo must not be larger than 5MB.',
            'profile_photo.dimensions' => 'The profile photo has invalid dimensions (max 2000x2000px).',
        ];
    }
}
