<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    public function index(Request $request)
    {
        $preferences = $request->user()
            ->preferences()
            ->with(['category', 'newsSource'])
            ->get();

        return response()->json($preferences);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'news_source_id' => 'nullable|exists:news_sources,id',
            'preferred_author' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Ensure at least one preference is set
        if (!$request->category_id && !$request->news_source_id && !$request->preferred_author) {
            return response()->json([
                'message' => 'At least one preference must be set'
            ], 422);
        }

        $preference = $request->user()->preferences()->create($request->all());

        return response()->json($preference->load(['category', 'newsSource']), 201);
    }

    public function update(Request $request, UserPreference $preference)
    {
        // Check if the preference belongs to the authenticated user
        if ($preference->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:categories,id',
            'news_source_id' => 'nullable|exists:news_sources,id',
            'preferred_author' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $preference->update($request->all());

        return response()->json($preference->load(['category', 'newsSource']));
    }

    public function destroy(Request $request, UserPreference $preference)
    {
        // Check if the preference belongs to the authenticated user
        if ($preference->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $preference->delete();

        return response()->json(['message' => 'Preference deleted successfully']);
    }
}
