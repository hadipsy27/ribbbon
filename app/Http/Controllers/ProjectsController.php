<?php

namespace App\Http\Controllers;

use App\Projectuser;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Project;
use App\Task;
use App\Credential;
use App\Helpers\Helpers;

class ProjectsController extends BaseController {

	// Returns the given project view
	public function show($id)
	{   	
		$project 		=	Project::find($id);

        // Must be refactored as a filter
		if ( $project->isOwner() == false && $project->isMember() == false ) {
			return Redirect::to('/hud');
		}

		return  View::make('ins/projects/show')->with('pTitle', $project->name);
	}

	// Get all user projects
	public function getAllUserProjects(){
		$projects = Project::where('user_id',Auth::id())->get();

		if($projects) {
			foreach ($projects as $project) {
				$completedWeight = Project::find($project->id)->tasks()->where('state','=','complete')->sum('weight');
				$totalWeight = Project::find($project->id)->tasks()->sum('weight');

				$project["completedWeight"] = $completedWeight;
				$project["totalWeight"] = $totalWeight;
			}
		}

		return $this->setStatusCode(200)->makeResponse('Taks retrieved successfully',$projects->toArray());
	}

    // Get all projects that the Auth user is a member of
	public function getAllMemberProjects(){
        $sharedProjects = Projectuser::where('user_id', Auth::id())->select('project_id')->get();
        $project_ids = [];

        foreach($sharedProjects as $project){
            $project_ids[] = $project->project_id;
        }

        $sharedProjects = Project::whereIn('id', $project_ids)->get();

        if($sharedProjects) {
            foreach ($sharedProjects as $project) {
                $completedWeight = Project::find($project->id)->tasks()->where('state','=','complete')->sum('weight');
                $totalWeight = Project::find($project->id)->tasks()->sum('weight');

                $project["completedWeight"] = $completedWeight;
                $project["totalWeight"] = $totalWeight;
            }
        }
        return $this->setStatusCode(200)->makeResponse('Taks retrieved successfully',$sharedProjects);
    }

	//	Return the given project
	public function getProject($id){
		if (!Project::find($id)) {
			return $this->setStatusCode(404)->makeResponse('The task was not found');
		}

		$project = Project::find($id);
		$project->tasks = Task::where('project_id', $id)->get();
		$project->credentials = Credential::where('project_id', $id)->get();

		return $this->setStatusCode(200)->makeResponse('Task was successfully found', $project);
	}

	// Insert the given project into the database
	public function storeProject(){
		if (!Input::all() || strlen(trim(Input::get('name'))) == 0) {
			return $this->setStatusCode(406)->makeResponse('No information provided to create task');
		}

		Input::merge(array('user_id' => Auth::id()));
		Project::create(Input::all());
		$id = \DB::getPdo()->lastInsertId();

		return $this->setStatusCode(200)->makeResponse('Task created successfully', Project::find($id));
	}

	// Update the given project
	public function updateProject($id){
		if ( Input::get('name') === "") {
			return $this->setStatusCode(406)->makeResponse('The task needs a name');
		}

		if (!Project::find($id)) {
			return $this->setStatusCode(404)->makeResponse('Task not found');
		}

		$input = Input::all();
		unset($input['_method']);

		Project::find($id)->update($input);
		return $this->setStatusCode(200)->makeResponse('The task has been updated');
	}

    public function getOwner($id){
        $owner_id = Project::whereId($id)->pluck('user_id');
        $owner = User::whereId($owner_id)->get();

        return $this->setStatusCode(200)->makeResponse('ok.', $owner[0]);
    }

    public function getMembers($id){
        $members_id = Projectuser::where('project_id', $id)->lists('user_id');
        $members = [];

        foreach($members_id as $id){
            $member = User::whereId($id)->get();
            array_push($members, $member[0]);
        }

        return $this->setStatusCode(200)->makeResponse('ok.', $members);
    }
    // Invites a user to the given project.
	public function invite($project_id, $nim){
        if(trim(strlen($nim)) == 0){
            return $this->setStatusCode(406)->makeResponse('The Registration Number field is required!');
        }

        if(!filter_var($nim, FILTER_VALIDATE_INT)) {
            return $this->setStatusCode(406)->makeResponse('Please enter a valid Registration Number!');
        }

        $project_name	= Project::find($project_id)->pluck('name');
        // $owner_id	    = Project::find($project_id)->pluck('user_id');
        $project_url 	= url() . '/projects/'.$project_id;
        $invited_user   = User::whereNim($nim)->get();

        if( count($invited_user) == 0 ){
            return $this->setStatusCode(406)->makeResponse('That user does not have an account.');
        }
        $invited_user = $invited_user[0];

        if( count(Projectuser::whereUserId($invited_user->id)->whereProjectId($project_id)->get()) != 0 ){
					return $this->setStatusCode(406)->makeResponse('A user with that Registration Number has already been invited.');
				}

        // if(Auth::id() != $owner_id){
        //     return $this->setStatusCode(406)->makeResponse('Only the project owner can invite a user.');
        // }
		// Save the relationship between user and project.
		$pu				= 	new Projectuser();
		$pu->project_id	=	$project_id;
		$pu->user_id	=	$invited_user->id;
		$pu->save();
		return $this->setStatusCode(200)->makeResponse('A new member has been added to this task.', $invited_user);

		// Helpers::sendProjectInviteMail($email, $project_name, $project_url);
	}

    // Removes a member from a given project
	public function removeMember($project_id, $member_id){
		if( count(Projectuser::whereUserId($member_id)->whereProjectId($project_id)->get()) == 0 ){
			return $this->setStatusCode(406)->makeResponse('That user is not in this task.');
		}

		$project = Project::find($project_id);
		$project->members()->detach($member_id);

		return $this->setStatusCode(200)->makeResponse('Member has been removed from this task.');
	}

}