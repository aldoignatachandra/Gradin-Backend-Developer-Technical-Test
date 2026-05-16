<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Courier::query();

        // Search by name (partial match, supports multi-word)
        if ($request->filled('search')) {
            $searchTerms = explode(' ', $request->input('search'));
            foreach ($searchTerms as $term) {
                $query->where('name', 'like', "%{$term}%");
            }
        }

        // Filter by level (supports comma-separated: ?level=2,3)
        if ($request->filled('level')) {
            $levels = array_map('intval', explode(',', $request->input('level')));
            $query->whereIn('level', $levels);
        }

        // Sort: default by name, override by registered_at
        $sortField = $request->input('sort', 'name');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortField, ['name', 'registered_at'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        $couriers = $query->paginate($request->input('per_page', 15));

        return response()->json($couriers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create($request->validated());

        return response()->json($courier, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Courier $courier): JsonResponse
    {
        return response()->json($courier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourierRequest $request, Courier $courier): JsonResponse
    {
        $courier->update($request->validated());

        return response()->json($courier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return response()->json(null, 204);
    }
}
