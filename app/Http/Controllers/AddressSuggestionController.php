<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressSuggestionRequest;
use App\Support\GooglePlaces;
use Illuminate\Http\JsonResponse;

class AddressSuggestionController extends Controller
{
    /**
     * Address suggestions for the public search box. Google is called server-side so the
     * API key never reaches the browser; failures return an empty list.
     */
    public function __invoke(AddressSuggestionRequest $request): JsonResponse
    {
        $apiKey = GooglePlaces::configuredApiKey();

        if ($apiKey === null) {
            return response()->json([]);
        }

        try {
            $suggestions = (new GooglePlaces($apiKey))->suggestAddresses(
                trim($request->string('query')->toString()),
                GooglePlaces::sessionToken(),
            );
        } catch (\Exception $e) {
            report($e);

            $suggestions = [];
        }

        return response()->json($suggestions);
    }
}
