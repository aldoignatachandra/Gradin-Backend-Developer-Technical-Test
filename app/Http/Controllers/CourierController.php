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
     * List all couriers
     *
     * Retrieve a paginated list of couriers with optional search, filtering, and sorting capabilities.
     *
     * @param Request $request
     * @return JsonResponse
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
     * Create a new courier
     *
     * Store a new courier in the database with the provided information.
     *
     * @param StoreCourierRequest $request
     * @return JsonResponse
     */
    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create($request->validated());

        return response()->json($courier, 201);
    }

    /**
     * Get a single courier
     *
     * Retrieve the details of a specific courier by its ID.
     *
     * @param Courier $courier
     * @return JsonResponse
     */
    public function show(Courier $courier): JsonResponse
    {
        return response()->json($courier);
    }

    /**
     * Update a courier
     *
     * Update the information of an existing courier.
     *
     * @param UpdateCourierRequest $request
     * @param Courier $courier
     * @return JsonResponse
     */
    public function update(UpdateCourierRequest $request, Courier $courier): JsonResponse
    {
        $courier->update($request->validated());

        return response()->json($courier);
    }

    /**
     * Delete a courier
     *
     * Remove a courier from the database.
     *
     * @param Courier $courier
     * @return JsonResponse
     */
    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return response()->json(null, 204);
    }
}
