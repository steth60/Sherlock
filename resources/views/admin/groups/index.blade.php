@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">

            <h1 class="h2 text-white">Admin - Groups Managment</h1>

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

    <div class="accordion card" id="groupAccordion">
        <ul id="group-list" class="list-group">
            @foreach($groups as $index => $group)
                <li class="list-group-item p-0 mb-2" data-id="{{ $group->id }}">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $group->id }}">
                            <button class="accordion-button @if($index != 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $group->id }}" aria-expanded="@if($index == 0) true @else false @endif" aria-controls="collapse{{ $group->id }}">
                                {{ $group->name }}
                            </button>
                        </h2>
                        <div id="collapse{{ $group->id }}" class="accordion-collapse collapse @if($index == 0) show @endif" aria-labelledby="heading{{ $group->id }}" data-bs-parent="#groupAccordion">
                            <div class="accordion-body">
                                <form action="{{ route('admin.groups.assignPermissions', $group) }}" method="POST" class="mb-3">
                                    @csrf
                                    <h5>Assign Permissions</h5>
                                    <div class="permission-tree">
                                        @foreach($permissions->whereNull('parent_id') as $parentPermission)
                                            @include('admin.groups.permission_tree_item', ['permission' => $parentPermission, 'group' => $group, 'depth' => 0])
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
                                        <div class="btn-group mb-2 me-2">
                                            <button class="btn drp-icon btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton{{ $group->id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="feather icon-more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $group->id }}">
                                                <a class="dropdown-item" href="#" onclick="showRenameModal('{{ $group->id }}', '{{ $group->name }}')">
                                                    <i class="feather icon-edit"></i> Rename
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" onclick="showDeleteModal('{{ $group->id }}')">
                                                    <i class="feather icon-trash-2"></i> Delete
                                                </a>
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
        <div class="modal-dialog modal-dialog-centered">
            <form id="renameForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="renameModalLabel">Rename Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="newGroupName">New Group Name</label>
                            <input type="text" class="form-control" id="newGroupName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this group? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitle"></strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
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


        }
    });

    function toggleReordering() {
        sortable.option("disabled", !sortable.option("disabled"));
        if (sortable.option("disabled")) {
            showToast('Reordering', 'Reordering disabled');
        } else {
            showToast('Reordering', 'Reordering enabled');
        }
    }

    function showRenameModal(groupId, groupName) {
        document.getElementById('newGroupName').value = groupName;
        var form = document.getElementById('renameForm');
        form.action = '/admin/groups/' + groupId + '/rename';
        var renameModal = new bootstrap.Modal(document.getElementById('renameModal'));
        renameModal.show();
    }

    function showDeleteModal(groupId) {
        var form = document.getElementById('deleteForm');
        form.action = '/admin/groups/' + groupId;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    function showToast(title, message) {
        document.getElementById('toastTitle').textContent = title;
        document.getElementById('toastMessage').textContent = message;
        var toast = new bootstrap.Toast(document.getElementById('liveToast'));
        toast.show();
    }

    $(document).ready(function() {
        $('.parent-permission-checkbox').on('change', function() {
            var $this = $(this);
            var $childrenContainer = $this.closest('.permission-item').next('.children');
            var $childCheckboxes = $childrenContainer.find('input[type="checkbox"]');
            var $toggleIcon = $this.siblings('label').find('.toggle-children');
            
            if ($this.is(':checked')) {
                $childCheckboxes.prop('checked', true);
                $childCheckboxes.prop('disabled', true);
                $childrenContainer.addClass('d-none');
                $toggleIcon.removeClass('fa-caret-down').addClass('fa-caret-right');
            } else {
                $childCheckboxes.prop('disabled', false);
                $childrenContainer.removeClass('d-none');
                $toggleIcon.removeClass('fa-caret-right').addClass('fa-caret-down');
                // Restore original state of child checkboxes
                $childCheckboxes.each(function() {
                    $(this).prop('checked', $(this).data('original-state'));
                });
            }
        });

        $('.toggle-children').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var $parentCheckbox = $this.closest('label').siblings('input[type="checkbox"]');
            
            if (!$parentCheckbox.is(':checked')) {
                $this.toggleClass('fa-caret-right fa-caret-down');
                $this.closest('.permission-item').next('.children').toggleClass('d-none');
            }
        });

        // Store original state of all checkboxes
        $('input[type="checkbox"]').each(function() {
            $(this).data('original-state', $(this).is(':checked'));
        });

        // Initialize parent checkboxes
        $('.parent-permission-checkbox').each(function() {
            var $this = $(this);
            var $childrenContainer = $this.closest('.permission-item').next('.children');
            var $childCheckboxes = $childrenContainer.find('input[type="checkbox"]');
            var $toggleIcon = $this.siblings('label').find('.toggle-children');
            
            if ($this.is(':checked')) {
                $childCheckboxes.prop('checked', true);
                $childCheckboxes.prop('disabled', true);
                $childrenContainer.addClass('d-none');
                $toggleIcon.removeClass('fa-caret-down').addClass('fa-caret-right');
            } else {
                $childrenContainer.removeClass('d-none');
                $toggleIcon.removeClass('fa-caret-right').addClass('fa-caret-down');
            }
        });

        // Handle form submission
        $('form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData($form[0]);

            // Remove existing permission inputs
            formData.delete('permissions[]');

            // Add all checked and disabled (parent) permissions
            $form.find('input[type="checkbox"]:checked, input[type="checkbox"]:disabled').each(function() {
                formData.append('permissions[]', $(this).val());
            });

            $.ajax({
                url: $form.attr('action'),
                method: $form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showToast('Success', response.message);
                    // Close modal if it's open
                    $('.modal').modal('hide');
                    // Reload page or update UI as needed
                    location.reload();
                },
                error: function(xhr) {
                    showToast('Error', xhr.responseJSON.message || 'An error occurred');
                }
            });
        });
    });
</script>
@endsection