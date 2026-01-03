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
        if ($request->has('province_id')) {
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

    public function getRegions(Request $request): JsonResponse
    {
        $query = Province::query()->with(['cities.regions', 'cities.province'])->orderBy('name');


        if ($request->has('city_id')) {
            $query->whereHas('cities', function ($q) use ($request) {
               $q->where('id',$request->city_id);
            });
        }

        // Filter by has regions
        if ($request->has('has_regions') && $request->boolean('has_regions')) {
            $query->whereHas('cities.regions');
        }

        // Search by name (province, city, or region)
        if ($request->has('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhereHas('cities', function ($cityQuery) use ($search) {
                        $cityQuery->where('name', 'like', $search)
                            ->orWhereHas('regions', function ($regionQuery) use ($search) {
                                $regionQuery->where('name', 'like', $search);
                            });
                    })
                    ->orWhereHas('regions', function ($regionQuery) use ($search) {
                        $regionQuery->where('name', 'like', $search);
                    });
            });
        }

        $res = $query->get();

        return $this->successResponse(
            [
            'provinces' => $res->map(function ($province) {
                return [
                    'id' => $province->id,
                    'name' => $province->name,
                    'cities' => $province->cities?->map(function ($city) {
                        return [
                            'id' => $city->id,
                            'name' => $city->name,
                            'province_id' => $city->province_id,
                            'province_name' => $city->province->name,
                            'regions' => $city->regions?->map(function ($region) {
                                return [
                                    'id' => $region->id,
                                    'name' => $region->name,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
            ],
            'Cities retrieved successfully'
        );
    }

    /**
     * Get industries
     */
    public function getIndustries(Request $request): JsonResponse
    {
        $industries = Industry::query()->orderBy('name')->get();

        return $this->successResponse(
        [
                'industries' => $industries->map(function ($industry) {
                    return [
                        'id' => $industry->id,
                        'name' => $industry->name,
                    ];
                }),
            ],
            'Industries retrieved successfully'
        );
    }

    /**
     * Get job groups
     */
    public function getJobGroups(Request $request): JsonResponse
    {
        $query = JobGroup::with('industry', 'parent')->orderBy('name');

        // Filter by industry_id if provided
        if ($request->has('industry_id')) {
            $query->where('industry_id', $request->industry_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $jobGroups = $query->get()->map(function ($jobGroup) {
            return [
                'id' => $jobGroup->id,
                'name' => $jobGroup->name,
                'industry_id' => $jobGroup->industry_id,
                'industry_name' => $jobGroup->industry->name,
                'parent_id' => $jobGroup->parent_id,
                'parent_name' => $jobGroup->parent?->name,
                'children' => $jobGroup->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'industry_id' => $child->industry_id,
                        'industry_name' => $child->industry->name,
                        'parent_id' => $child->parent_id,
                        'parent_name' => $child->parent?->name,
                    ];
                }),
            ];
        });

        return $this->successResponse([
            'jobGroups' => $jobGroups,
        ], 'Job groups retrieved successfully');
    }


    public function getAds()
    {
        $ads = \App\Models\Ads::all();
        return response()->json([
            'ads' => $ads
        ], 200);
    }
}
