<?php

namespace App\Http\Controllers\Api\Teams;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;
use App\Mail\SendInvToJoinTeamMail;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function invite(StoreInvitationRequest $request, Team $team)
    {
        if (!auth()->user()->isOwnerOfTeam($team->id)) {
            return response()->json(['not authorized'], 403);
        }

        if ($team->hasPendingInvite($request->email)) {
            return response()->json(['an invitation has already been sent to this user'], 422);
        }

        $recipient = User::where('email', $request->email)->first();

        // if recipient dont exist send inv email to register
        if (!$recipient) {
            $inv = Invitation::create([
                'team_id' => $team->id,
                'sender_id' => auth()->id(),
                'recipient_email' => $request->email,
                'token' => md5(uniqid(microtime()))
            ]);

            Mail::to($request->email)->send(new SendInvToJoinTeamMail($inv, false));

            return response()->json(['invitation sent'], 200);
        }

        if ($team->hasUser($recipient->id)) {
            return response()->json(['user already is a member'], 422);
        }

        $inv = Invitation::create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $request->email,
            'token' => md5(uniqid(microtime()))
        ]);

        Mail::to($request->email)->send(new SendInvToJoinTeamMail($inv, true));

        return response()->json(['inv sent to user'], 200);
    }

    public function resend(Invitation $invitation)
    {
        $recipient = User::where('email', $invitation->recipient_email)->get()->first();

        Mail::to($invitation->recipient_email)->send(new SendInvToJoinTeamMail($invitation, !is_null($recipient)));

        return response()->json(['inv resent to user'], 200);
    }

    public function respond(Request $request, Invitation $invitation)
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required'],
        ]);

        $token = $request->token;
        $decision = $request->decision;

        // check if inv belongs to this user
        if ($invitation->recipient_email !== auth()->user()->email) {
            return response()->json([
                'message' => 'not your inv'
            ], 401);
        }

        if ($invitation->token !== $token) {
            return response()->json([
                'message' => 'invalid token'
            ], 401);
        }

        if ($decision !== "deny") {
            auth()->user->teams()->attach($invitation->team->id);
        }

        $invitation->delete();
    }

    public function destroy(Invitation $invitation)
    {
        $invitation->delete();
    }
}
