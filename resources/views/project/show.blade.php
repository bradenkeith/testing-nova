@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-6">
    	<div class="card">
    	    <div class="row">
    	        <div class="col-md-12">
    	            <div class="card-header">
    	                <h4 class="card-title">Project: {{ $project->name }}</h4>
    	            </div>
    	            <div class="card-body">
    	            	User: {{ $email_address->name }}
    	            </div>
    	        </div>
    	    </div>
    	</div>
    </div>
    <div class="col-md-6">
        @include('shared.download-files')
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        @include('shared.upload-files')
    </div>
    <div class="col-md-6">
        @include('shared.files-already-uploaded')
    </div>
</div>

@endsection