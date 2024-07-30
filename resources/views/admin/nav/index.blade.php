@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Navigation Menu</h1>

    <button class="btn btn-primary mb-4" data-toggle="modal" data-target="#addMenuItemModal">Add Menu Item</button>
    <button class="btn btn-primary mb-4" data-toggle="modal" data-target="#addDropdownMenuModal">Add Dropdown Menu</button>

    <div id="menu-builder" class="mb-4">
        @foreach($menuItems as $menuItem)
            @include('admin.nav.menu-item', ['menuItem' => $menuItem, 'depth' => 0])
        @endforeach
    </div>

    <button id="save-order" class="btn btn-primary">Save Order</button>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="editMenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMenuItemModalLabel">Edit Menu Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editMenuItemForm">
                    <input type="hidden" name="id" id="menuItemId">
                    <div class="form-group">
                        <label for="menuItemTitle">Title</label>
                        <input type="text" class="form-control" id="menuItemTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="menuItemUrl">URL</label>
                        <input type="text" class="form-control" id="menuItemUrl" name="url" required>
                    </div>
                    <div class="form-group">
                        <label for="menuItemIcon">Icon</label>
                        <input type="text" class="form-control" id="menuItemIcon" name="icon">
                        <small class="form-text text-muted">Use Material Design Icons (e.g., mdi mdi-account).</small>
                    </div>
                    <div class="form-group">
                        <label for="menuItemParentId">Parent ID</label>
                        <input type="number" class="form-control" id="menuItemParentId" name="parent_id">
                    </div>
                    <div class="form-group">
                        <label for="menuItemPermission">Permission</label>
                        <input type="text" class="form-control" id="menuItemPermission" name="permission">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-danger" id="deleteMenuItem">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Menu Item Modal -->
<div class="modal fade" id="addMenuItemModal" tabindex="-1" role="dialog" aria-labelledby="addMenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMenuItemModalLabel">Add Menu Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addMenuItemForm">
                    <div class="form-group">
                        <label for="newMenuItemTitle">Title</label>
                        <input type="text" class="form-control" id="newMenuItemTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="newMenuItemUrl">URL</label>
                        <input type="text" class="form-control" id="newMenuItemUrl" name="url" required>
                    </div>
                    <div class="form-group">
                        <label for="newMenuItemIcon">Icon</label>
                        <input type="text" class="form-control" id="newMenuItemIcon" name="icon">
                        <small class="form-text text-muted">Use Material Design Icons (e.g., mdi mdi-account).</small>
                    </div>
                    <div class="form-group">
                        <label for="newMenuItemParentId">Parent ID</label>
                        <input type="number" class="form-control" id="newMenuItemParentId" name="parent_id">
                    </div>
                    <div class="form-group">
                        <label for="newMenuItemPermission">Permission</label>
                        <input type="text" class="form-control" id="newMenuItemPermission" name="permission">
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Dropdown Menu Modal -->
<div class="modal fade" id="addDropdownMenuModal" tabindex="-1" role="dialog" aria-labelledby="addDropdownMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDropdownMenuModalLabel">Add Dropdown Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addDropdownMenuForm">
                    <div class="form-group">
                        <label for="newDropdownMenuTitle">Title</label>
                        <input type="text" class="form-control" id="newDropdownMenuTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="newDropdownMenuIcon">Icon</label>
                        <input type="text" class="form-control" id="newDropdownMenuIcon" name="icon">
                        <small class="form-text text-muted">Use Material Design Icons (e.g., mdi mdi-account).</small>
                    </div>
                    <div class="form-group">
                        <label for="newDropdownMenuPermission">Permission</label>
                        <input type="text" class="form-control" id="newDropdownMenuPermission" name="permission">
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.13.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let menuBuilder = document.getElementById('menu-builder');

        new Sortable(menuBuilder, {
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '.handle',
            onEnd: function(evt) {
                // You can implement the saving of the order here if you wish
            }
        });

        // Initialize Sortable for each submenu
        document.querySelectorAll('.card-footer > .pl-4').forEach(function(el) {
            new Sortable(el, {
                group: 'nested',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                handle: '.handle',
                onEnd: function(evt) {
                    // Handle reordering here
                }
            });
        });

        document.querySelectorAll('.edit-menu-item').forEach(function(button) {
            button.addEventListener('click', function() {
                let menuItem = JSON.parse(this.getAttribute('data-menu-item'));
                document.getElementById('menuItemId').value = menuItem.id;
                document.getElementById('menuItemTitle').value = menuItem.title;
                document.getElementById('menuItemUrl').value = menuItem.url;
                document.getElementById('menuItemIcon').value = menuItem.icon;
                document.getElementById('menuItemParentId').value = menuItem.parent_id;
                document.getElementById('menuItemPermission').value = menuItem.permission;
                $('#editMenuItemModal').modal('show');
            });
        });

        document.getElementById('editMenuItemForm').addEventListener('submit', function(event) {
            event.preventDefault();
            let id = document.getElementById('menuItemId').value;
            let formData = new FormData(this);
            fetch('/admin/nav/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    $('#editMenuItemModal').modal('hide');
                    location.reload();
                }
            });
        });

        document.getElementById('deleteMenuItem').addEventListener('click', function() {
            let id = document.getElementById('menuItemId').value;
            fetch('/admin/nav/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'DELETE'
                }
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    $('#editMenuItemModal').modal('hide');
                    location.reload();
                }
            });
        });

        document.getElementById('addMenuItemForm').addEventListener('submit', function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            fetch('{{ route('admin.nav.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    $('#addMenuItemModal').modal('hide');
                    location.reload();
                }
            });
        });

        document.getElementById('addDropdownMenuForm').addEventListener('submit', function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            fetch('{{ route('admin.nav.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    $('#addDropdownMenuModal').modal('hide');
                    location.reload();
                }
            });
        });

        document.getElementById('save-order').addEventListener('click', function() {
            let order = [];
            function getOrder(parent, parentId = null) {
                parent.querySelectorAll(':scope > .card').forEach(function(item, index) {
                    order.push({ id: item.getAttribute('data-id'), order: index + 1, parent_id: parentId });
                    let sublist = item.querySelector('.card-footer > .pl-4');
                    if (sublist) {
                        getOrder(sublist, item.getAttribute('data-id'));
                    }
                });
            }
            getOrder(menuBuilder);

            // Save the order via AJAX
            fetch('{{ route('admin.menu.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            }).then(response => response.json()).then(data => {
                alert('Order saved successfully.');
            });
        });
    });
</script>
@endsection