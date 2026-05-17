<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CourierController extends Controller
{
    /**
     * List all couriers
     *
     * Retrieve a paginated list of couriers with optional search, filtering, and sorting capabilities.
     */
    public function index(Request $request): ResourceCollection
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

        return CourierResource::collection($couriers);
    }

    /**
     * Create a new courier
     *
     * Store a new courier in the database with the provided information.
     *
     * @return JsonResource
     */
    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create($request->validated());

        return (new CourierResource($courier))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get a single courier
     *
     * Retrieve the details of a specific courier by its ID.
     */
    public function show(Courier $courier): JsonResource
    {
        return new CourierResource($courier);
    }

    /**
     * Update a courier
     *
     * Update the information of an existing courier.
     */
    public function update(UpdateCourierRequest $request, Courier $courier): JsonResource
    {
        $courier->update($request->validated());

        return new CourierResource($courier);
    }

    /**
     * Delete a courier
     *
     * Remove a courier from the database.
     */
    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return response()->json(null, 204);
    }
}
