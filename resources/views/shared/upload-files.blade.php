<div class="card">
    <div class="row">
        <div class="col-md-12">
            <div class="card-header">
                <h4 class="card-title">Sign and Return</h4>
            </div>
        	<form 
        		id="returned-file"
        		method="post" 
        		action="{{ 
                    URL::signedRoute('returned-file',
                    [
                        'email_address' => $email_address->id, 
                        'project' => $project->id
                    ])
                }}"
        		enctype="multipart/form-data">
                
                <div class="card-body">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label>File</label>
                		<input type="file" name="file" class="form-control" />
                    </div>
                </div>
                <div class="card-footer ">
                    <button type="submit" class="btn btn-fill btn-info" id="returned-file-submit">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>