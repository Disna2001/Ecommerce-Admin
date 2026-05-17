<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    /**
     * Display a listing of the user's addresses.
     */
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($addresses);
    }

    /**
     * Store a newly created address.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:555',
            'city' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        $user = $request->user();

        // If this is the first address or is marked as default, unset other defaults
        $isDefault = $request->boolean('is_default') || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create([
            'tenant_id' => $user->tenant_id,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'],
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'] ?? 'Sri Lanka',
            'is_default' => $isDefault,
        ]);

        return response()->json([
            'message' => 'Address created successfully',
            'address' => $address
        ], 201);
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'required|string|max:555',
            'city' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault && !$address->is_default) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'],
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'] ?? 'Sri Lanka',
            'is_default' => $isDefault || $address->is_default, // keep true if it was already default
        ]);

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => $address
        ]);
    }

    /**
     * Remove the specified address.
     */
    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $wasDefault = $address->is_default;
        
        $address->delete();

        // If we deleted the default address, make the most recent one default
        if ($wasDefault) {
            $nextDefault = $request->user()->addresses()->orderBy('created_at', 'desc')->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * Set the address as default.
     */
    public function makeDefault(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'Address set as default',
            'address' => $address
        ]);
    }
}
