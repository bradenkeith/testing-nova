<div class="card">
    <div class="row">
        <div class="col-md-12">
            <div class="card-header">
                <h4 class="card-title">Files You Already Returned</h4>
            </div>
            <div class="card-body">
                @foreach ($email_address->returnedFiles as $file)
                    <p>
                        <a href="{{ 
                            URL::signedRoute('download',
                            [
                                'email_address' => $email_address->id, 
                                'project' => $project->id, 
                                'file_path' => base64_encode($file->file_path)
                            ]) 
                        }}" class="btn btn-primary btn-wd">Download</a>
                        Date Added: {{ $file->created_at->tz('America/New_York')->toDayDateTimeString() }}
                    </p>
                @endforeach

            </div>
        </div>
    </div>
</div>