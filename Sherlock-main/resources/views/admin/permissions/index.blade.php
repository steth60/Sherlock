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
                    <h5 class="card-title">Permissions Tree</h5>
                    <ul id="permissionsTree" class="list-group">
                        @foreach($rootPermissions as $permission)
                            <li class="list-group-item" data-id="{{ $permission->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="permission-name">{{ $permission->name }}</span>
                                    <div>
                                        <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $permission->id }}" data-name="{{ $permission->name }}">Edit</button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $permission->id }}">Delete</button>
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
                        <div class="form-group">
                            <label for="parentPermission">Parent Permission</label>
                            <select class="form-control" id="parentPermission" name="parent_id">
                                <option value="">None</option>
                                @foreach($allPermissions as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Permission</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editPermissionName">Permission Name</label>
                            <input type="text" class="form-control" id="editPermissionName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="editParentPermission">Parent Permission</label>
                            <select class="form-control" id="editParentPermission" name="parent_id">
                                <option value="">None</option>
                                @foreach($allPermissions as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
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
    $(document).ready(function() {
        // Handle permission click
        $('#permissionsTree').on('click', '.permission-name', function() {
            var $li = $(this).closest('li');
            var permissionId = $li.data('id');
            
            // If children are already loaded, just toggle their visibility
            if ($li.children('ul').length > 0) {
                $li.children('ul').toggle();
                return;
            }

            // Load child permissions
            $.get('/admin/permissions/' + permissionId + '/children', function(data) {
                var $childList = $('<ul class="list-group mt-2"></ul>');
                data.forEach(function(child) {
                    $childList.append(
                        '<li class="list-group-item" data-id="' + child.id + '">' +
                            '<div class="d-flex justify-content-between align-items-center">' +
                                '<span class="permission-name">' + child.name + '</span>' +
                                '<div>' +
                                    '<button class="btn btn-sm btn-primary edit-btn" data-id="' + child.id + '" data-name="' + child.name + '">Edit</button>' +
                                    '<button class="btn btn-sm btn-danger delete-btn" data-id="' + child.id + '">Delete</button>' +
                                '</div>' +
                            '</div>' +
                        '</li>'
                    );
                });
                $li.append($childList);
            });
        });

        // Handle edit button click
        $('#permissionsTree').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#editPermissionName').val(name);
            $('#editForm').attr('action', '/admin/permissions/' + id + '/rename');
            $('#editModal').modal('show');
        });

        // Handle delete button click
        $('#permissionsTree').on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            $('#deleteForm').attr('action', '/admin/permissions/' + id);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endsection