<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Company\CreateCompanyAction;
use App\Actions\Company\DeleteCompanyAction;
use App\Actions\Company\UpdateCompanyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orgId = $request->user()->organization_id;

        $companies = QueryBuilder::for(Company::class)
            ->where('organization_id', $orgId)
            ->allowedFilters(
                'name',
                'domain',
                'industry',
                AllowedFilter::exact('owner_id'),
            )
            ->allowedSorts('name', 'annual_revenue', 'employees_count', 'created_at')
            ->allowedIncludes('owner', 'contacts')
            ->withCount(['contacts', 'deals'])
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 15));

        return CompanyResource::collection($companies);
    }

    public function store(
        StoreCompanyRequest $request,
        CreateCompanyAction $action
    ): JsonResponse {
        $user = $request->user();
        $company = $action->execute($user->organization, $request->validated(), $user);

        return response()->json([
            'message' => 'Company created successfully',
            'data' => new CompanyResource($company->load('owner')),
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, Company $company): JsonResponse
    {
        abort_if($company->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $company->load(['owner', 'contacts'])->loadCount(['contacts', 'deals']);

        return response()->json([
            'data' => new CompanyResource($company),
        ]);
    }

    public function update(
        UpdateCompanyRequest $request,
        Company $company,
        UpdateCompanyAction $action
    ): JsonResponse {
        abort_if($company->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $updatedCompany = $action->execute($company, $request->validated());

        return response()->json([
            'message' => 'Company updated successfully',
            'data' => new CompanyResource($updatedCompany),
        ]);
    }

    public function destroy(
        Request $request,
        Company $company,
        DeleteCompanyAction $action
    ): JsonResponse {
        abort_if(! $request->user()->can('companies.delete'), Response::HTTP_FORBIDDEN);
        abort_if($company->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $action->execute($company);

        return response()->json([
            'message' => 'Company deleted successfully',
        ]);
    }
}
