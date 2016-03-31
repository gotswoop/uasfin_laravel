@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>
                <div class="panel-body">
                    <p>Please contact us at <a href="mailto:uashelp@usc.edu?subject=[via {{{ request()->server->get('SERVER_NAME') }}}] Requesting access to UASFIN">uashelp@usc.edu</a> to request access.</p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
