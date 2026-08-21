<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Contact\CreateContactAction;
use App\Actions\Contact\DeleteContactAction;
use App\Actions\Contact\UpdateContactAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Requests\Contact\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orgId = $request->user()->organization_id;

        $contacts = QueryBuilder::for(Contact::class)
            ->where('organization_id', $orgId)
            ->allowedFilters(
                'first_name',
                'last_name',
                'email',
                'lifecycle_stage',
                'department',
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('assigned_user_id'),
            )
            ->allowedSorts('first_name', 'last_name', 'email', 'lifecycle_stage', 'created_at')
            ->allowedIncludes('company', 'assignedUser')
            ->with(['company', 'assignedUser'])
            ->withCount(['deals', 'activities'])
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 15));

        return ContactResource::collection($contacts);
    }

    public function store(
        StoreContactRequest $request,
        CreateContactAction $action
    ): JsonResponse {
        $user = $request->user();
        $contact = $action->execute($user->organization, $request->validated(), $user);

        return response()->json([
            'message' => 'Contact created successfully',
            'data' => new ContactResource($contact->load('company', 'assignedUser')),
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, Contact $contact): JsonResponse
    {
        abort_if($contact->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $contact->load(['company', 'assignedUser'])->loadCount(['deals', 'activities']);

        return response()->json([
            'data' => new ContactResource($contact),
        ]);
    }

    public function update(
        UpdateContactRequest $request,
        Contact $contact,
        UpdateContactAction $action
    ): JsonResponse {
        abort_if($contact->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $updatedContact = $action->execute($contact, $request->validated());

        return response()->json([
            'message' => 'Contact updated successfully',
            'data' => new ContactResource($updatedContact),
        ]);
    }

    public function destroy(
        Request $request,
        Contact $contact,
        DeleteContactAction $action
    ): JsonResponse {
        abort_if(! $request->user()->can('contacts.delete'), Response::HTTP_FORBIDDEN);
        abort_if($contact->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $action->execute($contact);

        return response()->json([
            'message' => 'Contact deleted successfully',
        ]);
    }
}
