<div class="card">
    <div class="row">
        <div class="col-md-12">
            <div class="card-header">
                <h4 class="card-title">Download Files</h4>
            </div>
            <div class="card-body">
                @foreach ($project->projectFiles as $file)
                    <p>
                        <a href="{{ 
                            URL::signedRoute('download',
                            [
                                'email_address' => $email_address->id, 
                                'project' => $project->id, 
                                'file_path' => base64_encode($file->file_path)
                            ]) 
                        }}" class="btn btn-primary btn-wd">Download</a>
                        {{ $file->name }}
                    </p>
                @endforeach
            </div>
        </div>
    </div>
</div>