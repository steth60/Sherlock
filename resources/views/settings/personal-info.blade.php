
    
    
    
    

    <h5 class="m-b-10">Personal Information</h5>
    <div class="card-body">
        <form method="POST" action="{{ route('settings.personal-info.update') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
            </div>
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Personal Info</button>
        </form>
    </div>
