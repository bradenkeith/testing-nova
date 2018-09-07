<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ProjectController extends Controller
{
    public function show(
        Request $request,
        \App\Project $project,
        \App\EmailAddress $email_address
    ) {
        return view('project.show')
            ->with('project', $project)
            ->with('email_address', $email_address);
    }

    public function returnedFileUpload(
        Request $request,
        \App\Project $project,
        \App\EmailAddress $email_address
    ) {
        $returnedFile = \App\ReturnedFile::create([
            'project_id'       => $project->id,
            'email_address_id' => $email_address->id,
            'file_path'        => $request->file('file')->store('returned-files'),
        ]);

        return redirect(
                URL::signedRoute('projects',
                    ['email_address'=> $email_address->id, 'project'=>$project->id])
            )
            ->with('status', 'File Successfully Uploaded!');
    }

    public function download(
        Request $request,
        \App\Project $project,
        \App\EmailAddress $email_address,
        $file_path
    ) {
        return Storage::download(base64_decode($file_path));
    }
}
