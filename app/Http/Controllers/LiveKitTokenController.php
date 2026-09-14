<?php

namespace App\Http\Controllers;

use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\VideoGrant;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveKitTokenController extends Controller
{
    public function __invoke(Request $request, Meeting $meeting): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 401);

        $meeting->refresh();

        abort_unless(
            $meeting->status === 'active',
            403,
            'This meeting is not active.'
        );

        $isOrganizer =
            (string) $meeting->organizer_id === (string) $user->id;

        $isParticipant = $meeting->participants()
            ->where('user_id', $user->id)
            ->exists();

        abort_unless(
            $isOrganizer || $isParticipant,
            403,
            'You are not authorized to join this meeting.'
        );

        $apiKey = (string) config('livekit.api_key');
        $apiSecret = (string) config('livekit.api_secret');
        $serverUrl = (string) config('livekit.url');

        abort_if(
            $apiKey === '' || $apiSecret === '' || $serverUrl === '',
            500,
            'LiveKit is not configured.'
        );

        $identity = 'user-' . $user->id;
        $roomName = 'smartmeet-meeting-' . $meeting->id;

        $tokenOptions = (new AccessTokenOptions())
            ->setIdentity($identity)
            ->setTtl(10 * 60);

        $videoGrant = (new VideoGrant())
            ->setRoomJoin()
            ->setRoomName($roomName)
            ->setCanPublish()
            ->setCanSubscribe();

        $token = (new AccessToken($apiKey, $apiSecret))
            ->init($tokenOptions)
            ->setGrant($videoGrant)
            ->toJwt();

        return response()->json([
            'server_url' => $serverUrl,
            'token' => $token,
            'room' => $roomName,
            'identity' => $identity,
            'is_organizer' => $isOrganizer,
        ]);
    }
}
