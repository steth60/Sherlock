@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Groups</h1>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn btn-info mb-3" onclick="toggleReordering()">Toggle Reordering</button>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Create Group</h5>
                    <form action="{{ route('admin.groups.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Group Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight</label>
                            <input type="number" class="form-control" id="weight" name="weight" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Group</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Groups</h5>
                    <p class="card-text display-4">{{ $groups->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <p class="mb-3">You can drag and drop the groups to reorder them. Click the "Toggle Reordering" button to enable or disable reordering.</p>

    <div id="accordion3" class="accordion">
        <ul id="group-list" class="list-group">
            @foreach($groups as $index => $group)
                <li class="list-group-item p-0 mb-2" data-id="{{ $group->id }}">
                    <div class="card mb-0">
                        <div class="card-header d-flex justify-content-between align-items-center p-2" id="heading{{ $index }}">
                            <button class="btn btn-link p-0 m-0 @if($index != 0) collapsed @endif" data-toggle="collapse" data-target="#collapse{{ $index }}" aria-expanded="@if($index == 0) true @else false @endif" aria-controls="collapse{{ $index }}">
                                {{ $group->name }}
                            </button>
                        </div>
                        <div id="collapse{{ $index }}" class="collapse @if($index == 0) show @endif" aria-labelledby="heading{{ $index }}" data-parent="#accordion3">
                            <div class="card-body">
                                <form action="{{ route('admin.groups.assignPermissions', $group) }}" method="POST" class="mb-3">
                                    @csrf
                                    <h5>Assign Permissions</h5>
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission{{ $group->id }}-{{ $permission->id }}" {{ $group->permissions->contains($permission->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission{{ $group->id }}-{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="btn btn-secondary mt-3">Assign Permissions</button>
                                </form>
                                <form action="{{ route('admin.groups.assignGroups', $group) }}" method="POST">
                                    @csrf
                                    <h5>Assign Groups (Inheritance)</h5>
                                    <div class="row">
                                        @foreach($groups as $grp)
                                            @if($grp->id !== $group->id)
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="groups[]" value="{{ $grp->id }}" id="group{{ $group->id }}-{{ $grp->id }}" {{ $group->childGroups->contains($grp->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="group{{ $group->id }}-{{ $grp->id }}">
                                                            {{ $grp->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <button type="submit" class="btn btn-secondary">Assign Groups</button>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $index }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $index }}">
                                                <a class="dropdown-item" href="#" onclick="showRenameModal('{{ $group->id }}', '{{ $group->name }}')">Rename</a>
                                                <a class="dropdown-item text-danger" href="#" onclick="showDeleteModal('{{ $group->id }}')">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Rename Modal -->
    <div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="renameForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="renameModalLabel">Rename Group</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="newGroupName">New Group Name</label>
                            <input type="text" class="form-control" id="newGroupName" name="name" required>
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
                        <h5 class="modal-title" id="deleteModalLabel">Delete Group</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this group? This action cannot be undone.</p>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    var sortable = new Sortable(document.getElementById('group-list'), {
        animation: 150,
        disabled: true,
        onEnd: function(evt) {
            var order = [];
            document.querySelectorAll('#group-list li').forEach((el, index) => {
                order.push(el.getAttribute('data-id'));
            });

            fetch("{{ route('admin.groups.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            });
        }
    });

    function toggleReordering() {
        sortable.option("disabled", !sortable.option("disabled"));
        if (sortable.option("disabled")) {
            alert('Reordering disabled');
        } else {
            alert('Reordering enabled');
        }
    }

    function showRenameModal(groupId, groupName) {
        document.getElementById('newGroupName').value = groupName;
        var form = document.getElementById('renameForm');
        form.action = '/admin/groups/' + groupId + '/rename';
        $('#renameModal').modal('show');
    }

    function showDeleteModal(groupId) {
        var form = document.getElementById('deleteForm');
        form.action = '/admin/groups/' + groupId;
        $('#deleteModal').modal('show');
    }
</script>
@endsection
