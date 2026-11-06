<?php

namespace App\Http\Controllers;

use App\Services\Leaderboard\Leaderboard;
use App\Services\Refersion\RefersionApiException;
use App\Services\Refersion\RefersionAuthException;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{
    public function __invoke(Leaderboard $leaderboard): JsonResponse
    {
        try {
            return response()->json([
                'data' => $leaderboard->topFive(),
            ]);
        } catch (RefersionAuthException) {
            return response()->json([
                'message' => 'Refersion authentication failed.',
            ], 502);
        } catch (RefersionApiException) {
            return response()->json([
                'message' => 'Refersion request failed.',
            ], 502);
        }
    }
}
