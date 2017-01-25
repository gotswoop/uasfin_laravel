@extends('layouts.app')

@section('content')
{{-- */ $msg = $msg; /*--}}
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
               		<h4>{{ $msg['title'] }}</h4>
            		</div>	
				<div class="panel-body">
				    <p>{{ $msg['body'] }}</p>
				   	<p><a href="/account/dashboard"><button type="button" class="btn btn-md btn-info">Go to Dashboard</button></a></p>
	            </div>
            </div>
        </div>
    </div>
</div>
@endsection