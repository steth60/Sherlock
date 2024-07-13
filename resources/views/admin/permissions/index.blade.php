@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Permissions</h1>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn btn-info mb-3" data-toggle="modal" data-target="#createPermissionModal">Create Permission</button>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Permissions</h5>
                    <ul class="list-group">
                        @foreach($permissions as $index => $permission)
                            <li class="list-group-item d-flex justify-content-between align-items-center mb-4 border">
                                <span>{{ $permission->name }}</span>
                                <div class="dropdown">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $index }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $index }}">
                                        <a class="dropdown-item" href="#" onclick="showRenameModal('{{ $permission->id }}', '{{ $permission->name }}')">Rename</a>
                                        <a class="dropdown-item text-danger" href="#" onclick="showDeleteModal('{{ $permission->id }}')">Delete</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Permission Modal -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPermissionModalLabel">Create Permission</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="permissionName">Permission Name</label>
                            <input type="text" class="form-control" id="permissionName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Permission</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rename Modal -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="renameForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="renameModalLabel">Rename Permission</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="newPermissionName">New Permission Name</label>
                            <input type="text" class="form-control" id="newPermissionName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Permission</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this permission? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showRenameModal(permissionId, permissionName) {
        document.getElementById('newPermissionName').value = permissionName;
        var form = document.getElementById('renameForm');
        form.action = '/admin/permissions/' + permissionId + '/rename';
        $('#renameModal').modal('show');
    }

    function showDeleteModal(permissionId) {
        var form = document.getElementById('deleteForm');
        form.action = '/admin/permissions/' + permissionId;
        $('#deleteModal').modal('show');
    }
</script>
@endsection
