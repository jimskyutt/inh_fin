<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Barangay;
use App\Models\Service;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])->latest()->paginate(10);
        $barangays = Barangay::orderBy('brgy_name', 'asc')->get();
        $services = Service::orderBy('service_name', 'asc')->get();
        
        // Get current user's barangay
        $userBarangay = auth()->user()->address; // Assuming address contains the barangay name
        
        // Get service providers from the same barangay
        $suggestedProviders = User::where('role', User::ROLE_SERVICE_PROVIDER)
            ->where('status', User::STATUS_VERIFIED)
            ->where('address', 'like', "%{$userBarangay}%")
            ->with(['services' => function($query) {
                $query->select('service_name');
            }])
            ->take(5) // Limit to 5 suggested providers
            ->get()
            ->map(function($provider) {
                return (object)[
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'face_img' => $provider->face_img,
                    'barangay_name' => $provider->address,
                    'service_name' => $provider->services->first() ? $provider->services->first()->service_name : null
                ];
            });

        return view('page.newsfeed', compact('posts', 'barangays', 'services', 'suggestedProviders'));
    }

    public function store(Request $request)
    {
        $isAdmin = auth()->user()->role === 'Admin';
        
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'barangay_id' => $isAdmin ? 'nullable|exists:barangays,brgy_id' : 'required|exists:barangays,brgy_id',
            'service_id' => 'nullable|exists:services,service_id',
            'budget' => 'nullable|numeric|min:0'
        ]);

        $service_name = null;
        if (!empty($validated['service_id'])) {
            $service = Service::find($validated['service_id']);
            if ($service) {
                $service_name = $service->service_name;
            }
        }

        $post = new Post([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'barangay_name' => !empty($validated['barangay_id']) ? Barangay::find($validated['barangay_id'])->brgy_name : null,
            'service_name' => $service_name,
            'budget' => $validated['budget'] ?? null
        ]);

        $post->save();

        // Notify other homeowners about the new post
        NotificationService::notifyAboutNewPost($post);

        return redirect()->route('page.newsfeed')->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $barangays = Barangay::orderBy('brgy_name', 'asc')->get();
        $services = Service::orderBy('service_name', 'asc')->get();

        return view('page.edit-post', compact('post', 'barangays', 'services'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $isAdmin = auth()->user()->role === 'Admin';
        
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'barangay_id' => $isAdmin ? 'nullable|exists:barangays,brgy_id' : 'required|exists:barangays,brgy_id',
            'service_id' => 'nullable|exists:services,service_id',
            'budget' => 'nullable|numeric|min:0'
        ]);

        $updateData = [
            'content' => $validated['content']
        ];

        if (!$isAdmin) {
            $service_name = null;
            if (!empty($validated['service_id'])) {
                $service = Service::find($validated['service_id']);
                if ($service) {
                    $service_name = $service->service_name;
                }
            }

            $updateData = array_merge($updateData, [
                'barangay_name' => Barangay::find($validated['barangay_id'])->brgy_name,
                'service_name' => $service_name,
                'budget' => $validated['budget'] ?? null
            ]);
        }

        $post->update($updateData);

        return redirect()->route('page.newsfeed')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();

        return redirect()->route('page.newsfeed')->with('success', 'Post deleted successfully!');
    }
}
