<div class="card">
    <div class="card-header">
        <h5><i class="feather icon-book text-c-blue wid-20"></i><span class="p-l-5">Profile Photo Settings</span></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Initials with Different Background Colors -->
            <div class="col-md-12 mb-4">
                <h5>Select Initials with Background Color</h5>
                <div class="d-flex flex-wrap">
                    @foreach(['#FFB3BA', '#FFDFBA', '#FFFFBA', '#BAFFC9', '#BAE1FF', '#D4A5A5', '#FFB347', '#779ECB', '#B39EB5'] as $color)
                        <div class="profile-initials-preview" style="background-color: {{ $color }};">
                            <input type="radio" name="initial_color" value="{{ $color }}" class="d-none" id="color-{{ $loop->index }}">
                            <label for="color-{{ $loop->index }}" class="d-flex align-items-center justify-content-center text-white" style="width: 60px; height: 60px; cursor: pointer;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </label>
                        </div>
                    @endforeach
                    <div class="profile-initials-preview" data-bs-toggle="modal" data-bs-target="#uploadProfilePhotoModal" style="cursor: pointer; border: 2px dashed #ccc;">
                        <div class="d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                            <i class="feather icon-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <form action="{{ route('settings.profile-photo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="profile_icon">Select Profile Icon</label>
                <select class="form-control" id="profile_icon" name="profile_icon">
                    <option value="">Choose an icon</option>
                    <option value="icon1.png">Icon 1</option>
                    <option value="icon2.png">Icon 2</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
            <div class="form-group mt-3">
                <label for="initial_color">Selected Initial Background Color</label>
                <input type="hidden" id="selected_initial_color" name="initial_color">
                <div id="selected_initial_color_preview" class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 60px; height: 60px; font-size: 30px;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
        </form>
    </div>
</div>

<!-- Modal for Uploading Profile Photo -->
<div class="modal fade" id="uploadProfilePhotoModal" tabindex="-1" aria-labelledby="uploadProfilePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadProfilePhotoModalLabel">Upload Profile Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('settings.profile-photo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="profile_photo">Profile Photo</label>
                        <input type="file" class="form-control" id="profile_photo" name="profile_photo" required>
                    </div>
                    <div class="form-group mt-3">
                        <small class="text-muted">Max file size: 2MB. Allowed formats: JPG, PNG. Crop your image to a circular shape before uploading.</small>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Upload Photo</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="initial_color"]').forEach(function(input) {
            input.addEventListener('change', function() {
                document.getElementById('selected_initial_color').value = this.value;
                document.getElementById('selected_initial_color_preview').style.backgroundColor = this.value;
            });
        });
    });
</script>
@endsection
