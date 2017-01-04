@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
               		<h4>Institution Added Successfully!</h4>
            		</div>	
				<div class="panel-body">
				    <p>Your financial institution was succesfully added. However, it might take a few minutes until it shows up in your dashboard.</p>
				   	<p><a href="/account/dashboard"><button type="button" class="btn btn-md btn-info">Go to Dashboard</button></a></p>
	            </div>
            </div>
        </div>
    </div>
</div>
@endsection