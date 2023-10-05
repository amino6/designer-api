<?php

namespace App\Http\Controllers\Api\Teams;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TeamResource::collection(Team::with(["owner", "members"])->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTeamRequest $request)
    {
        $team = Team::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'owner_id' => auth()->id(),
        ]);

        $team->members()->attach(auth()->id());

        return new TeamResource($team);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        return new TeamResource($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTeamRequest $request, Team $team)
    {
        $this->authorize('update', $team);

        $team->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'owner_id' => auth()->id(),
        ]);

        $team->members()->attach(auth()->id());

        return new TeamResource($team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();

        return response()->json(['msg' => 'team deleted successfuly']);
    }

    public function getUserTeams()
    {
        return TeamResource::collection(auth()->user()->teams);
    }

    public function findBySlug($slug)
    {
        $team = Team::where('slug', $slug)->get();

        if ($team->count() > 0)
            return new TeamResource($team[0]);
        else
            return response()->json(['team not found'], 404);
    }
}
