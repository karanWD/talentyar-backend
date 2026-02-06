<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Models\Province;
use App\Models\Industry;
use App\Models\JobGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends BaseApiController
{
    /**
     * Get all provinces
     */
    public function getProvinces(Request $request): JsonResponse
    {
        $query = Province::query()->orderBy('name');

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $provinces = $query->get();

        return $this->successResponse(
            [
                'provinces' => $provinces->map(function ($province) {
                    return [
                        'id' => $province->id,
                        'name' => $province->name,
                    ];
                }),
            ],
            'Provinces retrieved successfully'
        );
    }

    /**
     * Get cities (optionally filtered by province)
     */
    public function getCities(Request $request): JsonResponse
    {
        $query = City::query()->orderBy('name');

        // Filter by province_id if provided
        if ($request->has('province_id') && !is_null($request->get('province_id'))) {
            $query->where('province_id', $request->province_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $cities = $query->get();

        return $this->successResponse(
            [
                'cities' => $cities->map(function ($city) {
                    return [
                        'id' => $city->id,
                        'name' => $city->name,
                        'province_id' => $city->province_id,
                        'province_name' => $city->province->name,
                    ];
                }),
            ],
            'Cities retrieved successfully'
        );
    }
}
