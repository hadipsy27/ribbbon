{{-- <form class="credential-form new-credential">
    <span v-if="msg.success != null" class="status-msg success-msg">@{{ msg.success }}</span>
    <span v-if="msg.error != null" class="status-msg error-msg">@{{ msg.error }}</span>
    <div class="form-group">
        <label>FTP/SSH</label> <input v-model="newCredential.type" type="radio" name="type" value="1">
        <label>Other</label> <input v-model="newCredential.type" type="radio" name="type" value="0" checked>
    </div>
    <div class="form-group">
        <label>Name</label>
        <input v-model="newCredential.name" class="form-control" type="text" name="name" placeholder="Name">
    </div>
    <div class="form-group">
        <label>Username</label>
        <input v-model="newCredential.username" class="form-control" type="text" name="username" placeholder="Username">
    </div>
    <div class="form-group">
        <label>Password</label>
        <input v-model="newCredential.password" class="form-control" type="text" name="password" placeholder="Password">
    </div>
    <div class="form-group type-@{{ newCredential.type }}">
        <div class="col-xs-6 no-padding-left">
            <label>Hostname</label>
            <input v-model="newCredential.hostname" class="form-control other" type="text" name="hostname" placeholder="Hostname">
        </div>
        <div class="col-xs-6 no-padding-right">
            <label>Port</label>
            <input v-model="newCredential.port" class="form-control other" type="text" name="port" placeholder="Port">
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group">
        <button v-on:click="createCredential(project.user_id, project.id)" class="btn btn-primary">Save</button>
    </div>
</form>
<hr>

<template v-if="project.credentials.length > 0">
    <table class="table table-striped">
        <thead><tr><th>Name</th><th>Username</th><th>Password</th><th>Hostname</th><th>Port</th><th>Actions</th></tr></thead>
        <tbody>
            <tr v-for="credential in project.credentials">
                <td>@{{ credential.name }}</th>
                <td>@{{ credential.username }}</td>
                <td>@{{ credential.password }}</td>
                <td>@{{ credential.hostname }}</td>
                <td>@{{ credential.port }}</td>
                <td style="font-size: 1.5em">
                    <a title="Edit" v-on:click="editCredential(credential)"><i class="ion-ios-color-wand-outline"></i></a>
                    <a title="Delete" v-on:click="deleteCredential(credential)"><i class="ion-ios-close-outline"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</template> --}}
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whooops!</strong>There ware some problems with your input. <br><br>
        <ul>
            @foreach ($errors as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismis="alert">x</button>
    <strong>{{ $message }}</strong>
</div>
<img src="/images/{{Session::get('path')}}">
@endif
{!! Form::open(array('route' => 'fileUpload','enctype' => 'multipart/form-data')) !!}
<div class="container" style="margin-top: 4%">
	<div class="row cancel">
		<div class="col-md-4">
			{!! Form::file('image', array('class' => 'image')) !!}
		</div>
		<div class="col-md-4">
			<button type="submit" class="btn btn-info" style="background-color: #26b2ad;">Upload</button>
            <button type="submit" class="btn btn-info"><a href="/images/{{Session::get('path')}}" style="color: white"> Download</a></button>
		</div>
        
    </div>
</div>
{!! Form::close() !!}