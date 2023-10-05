<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelDeleteErrorException;
use App\Http\Requests\Preference\PreferenceGetRequest;
use App\Http\Requests\Preference\PreferenceStoreRequest;
use App\Http\RequestTransformers\User\PreferenceStoreTransformer;
use App\Http\Services\PreferencesService;
use App\Models\Preference;
use Illuminate\Http\JsonResponse;

class PreferenceController extends Controller
{
    public function __construct(private PreferencesService $service)
    {
    }

    /**
     * @param PreferenceGetRequest $request
     * @return JsonResponse
     */
    public function index(PreferenceGetRequest $request): JsonResponse
    {
        return response()->json($this->service->getAllPreference($request->get('entity'), $this->getUser()));
    }

    /**
     * @param PreferenceStoreRequest $request
     * @return JsonResponse
     */
    public function store(PreferenceStoreRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->insertPreference(
                (new PreferenceStoreTransformer())->transform($request),
                $this->getUser(),
            ),
        );
    }

    public function show(Preference $preference): JsonResponse
    {
        return response()->json($this->service->show($preference));
    }

    public function update(PreferenceStoreRequest $request, Preference $preference): JsonResponse
    {
        return response()->json(
            $this->service->updatePreference(
                (new PreferenceStoreTransformer())->transform($request),
                $this->getUser(),
                $preference,
            ),
        );
    }

    /**
     * @param Preference $preference
     * @return JsonResponse
     * @throws ModelDeleteErrorException
     */
    public function destroy(Preference $preference): JsonResponse
    {
        $this->service->delete($preference, $this->getUser());

        return response()->json();
    }
}
