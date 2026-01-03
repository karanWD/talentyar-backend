<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Employer\StoreOrUpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends BaseApiController
{

    public function show(Request $request): JsonResponse
    {
        $company = Company::query()
            ->with(['employers', 'city', 'province', 'region', 'media'])
            ->where('id', auth('api-employer')->user()?->company_id);

        if (!$company->exists()) {
            return $this->errorResponse('Company not found', 404);
        }

        return $this->successResponse(new CompanyResource($company->first()), 'Company retrieved successfully');
    }

    public function storeOrUpdate(StoreOrUpdateCompanyRequest $request): JsonResponse
    {
        // TODO :: acl employer

        $employer = auth('api-employer')->user();
        $companyModel = Company::with(['employers', 'city', 'province', 'region', 'media'])
            ->where('id', $employer?->company_id)
            ->first();

        // Get all validated data excluding media fields
        $validatedData = $request->validated();
        $fillableFields = [
            'name',
            'registered_name',
            'description',
            'summary',
            'address',
            'phone',
            'email',
            'website',
            'size',
            'industry_ids',
            'city_id',
            'province_id',
            'region_id',
            'year_of_establishment',
            'activity_type',
            'ownership_type',
            'brand',
            'benefits_and_facilities',
        ];

        $companyData = array_intersect_key($validatedData, array_flip($fillableFields));

        // Convert benefits_and_facilities array to JSON
        if (isset($companyData['benefits_and_facilities']) && is_array($companyData['benefits_and_facilities'])) {
            $companyData['benefits_and_facilities'] = json_encode($companyData['benefits_and_facilities']);
        }

        if (isset($companyData['industry_ids']) && is_array($companyData['industry_ids'])) {
            $companyData['industry_ids'] = json_encode($companyData['industry_ids']);
        }

        $mediaMap = [
            'logo'    => Media::TYPE_LOGO,
            'doc'     => Media::TYPE_DOC,
            'profile' => Media::TYPE_PROFILE,
        ];

        if (!$companyModel) {
            $companyModel = Company::create($companyData);

            foreach ($mediaMap as $field => $type) {
                if (!$request->filled($field)) continue;

                $media = Media::where('hash', $request->get($field))
                    ->where('type', $type)
                    ->first();

                if (!$media) continue;

                $sameAlreadyAssigned = $companyModel->media()
                    ->where('type', $type)
                    ->where('id', $media->id)
                    ->exists();

                if ($sameAlreadyAssigned) {
                    continue;
                }

                $companyModel->media()->where('type', $type)->delete();
                $companyModel->assignMedia($media);
            }

            $employer->company_id = $companyModel->id;
            $employer->save();

            $companyModel->load(['employers', 'city', 'province', 'region', 'media']);

            return $this->successResponse(new CompanyResource($companyModel), 'Company stored or updated successfully');
        } else {
            $companyModel->update($companyData);

            foreach ($mediaMap as $field => $type) {
                if (!$request->filled($field)) continue;

                $media = Media::where('hash', $request->get($field))
                    ->where('type', $type)
                    ->first();

                if (!$media) continue;

                $sameAlreadyAssigned = $companyModel->media()
                    ->where('type', $type)
                    ->where('id', $media->id)
                    ->exists();

                if ($sameAlreadyAssigned) {
                    continue;
                }

                $companyModel->media()->where('type', $type)->delete();
                $companyModel->assignMedia($media);
            }


            $companyModel->load(['employers', 'city', 'province', 'region', 'media']);

            return $this->successResponse(new CompanyResource($companyModel), 'Company stored or updated successfully');
        }


    }
}





