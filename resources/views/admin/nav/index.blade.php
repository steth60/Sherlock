@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4">Manage Navigation Menu</h1>
            <p class="lead text-muted">Here you can manage your navigation menu items. Use the buttons below to add new items or dropdown menus. Drag and drop the items to reorder them. Click on the edit button to modify or delete existing items.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addMenuItemModal">Add Menu Item</button>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addDropdownMenuModal">Add Dropdown Menu</button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div id="menu-builder" class="list-group">
                @foreach($menuItems as $menuItem)
                    @include('admin.nav.menu-item', ['menuItem' => $menuItem, 'depth' => 0])
                @endforeach
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <button id="save-order" class="btn btn-success">Save Order</button>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editMenuItemModal" tabindex="-1" aria-labelledby="editMenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMenuItemModalLabel">Edit Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editMenuItemForm">
                    <input type="hidden" name="id" id="menuItemId">
                    <div class="mb-3">
                        <label for="menuItemTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="menuItemTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="menuItemUrl" class="form-label">URL</label>
                        <input type="text" class="form-control" id="menuItemUrl" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label for="menuItemIcon" class="form-label">Icon</label>
                        <input type="text" class="form-control" id="menuItemIcon" name="icon">
                        <small class="form-text text-muted">Use Material Design Icons (e.g., mdi mdi-account).</small>
                    </div>
                    <div class="mb-3">
                        <label for="menuItemParentId" class="form-label">Parent ID</label>
                        <input type="number" class="form-control" id="menuItemParentId" name="parent_id">
                    </div>
                    <div class="mb-3">
                        <label for="menuItemPermission" class="form-label">Permission</label>
                        <input type="text" class="form-control" id="menuItemPermission" name="permission">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-danger" id="deleteMenuItem">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Menu Item Modal -->
<div class="modal fade" id="addMenuItemModal" tabindex="-1" aria-labelledby="addMenuItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMenuItemModalLabel">Add Menu Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMenuItemForm">
                    <div class="mb-3">
                        <label for="newMenuItemTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="newMenuItemTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="newMenuItemUrl" class="form-label">URL</label>
                        <input type="text" class="form-control" id="newMenuItemUrl" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label for="newMenuItemIcon" class="form-label">Icon</label>
                        <input type="text" class="form-control" id="newMenuItemIcon" name="icon">
                        <small class="form-text text-muted">Use Material Design Icons (e.g., mdi mdi-account).</small>
                    </div>
                    <div class="mb-3">
                        <label for="newMenuItemParentId" class="form-label">Parent ID</label>
                        <input type="number" class="form-control" id="newMenuItemParentId" name="parent_id">
                    </div>
                    <div class="mb-3">
                        <label for="newMenuItemPermission" class="form-label">Permission</label>
                        <input type="text" class="form-control" id="newMenuItemPermission" name="permission">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Dropdown Menu Modal -->
<div class="modal fade" id="addDropdownMenuModal" tabindex="-1" aria-labelledby="addDropdownMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDropdownMenuModalLabel">Add Dropdown Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDropdownMenuForm">
                    <div class="mb-3">
                        <label for="newDropdownMenuTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="newDropdownMenuTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="newDropdownMenuIcon" class="form-label">Icon</label>
                        <input type="text" class="form-control" id="newDropdownMenuIcon" name="icon">
                        <small class="form-text text-muted">Use Material Design Icons (e.g., mdi mdi-account).</small>
                    </div>
                    <div class="mb-3">
                        <label for="newDropdownMenuPermission" class="form-label">Permission</label>
                        <input type="text" class="form-control" id="newDropdownMenuPermission" name="permission">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
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

    if (menuBuilder) {
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
    }

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
            document.getElementById('menuItemUrl').value = menuItem.url || '';
            document.getElementById('menuItemIcon').value = menuItem.icon || '';
            document.getElementById('menuItemParentId').value = menuItem.parent_id || '';
            document.getElementById('menuItemPermission').value = menuItem.permission || '';
            var editMenuItemModal = new bootstrap.Modal(document.getElementById('editMenuItemModal'));
            editMenuItemModal.show();
        });
    });

    let editMenuItemForm = document.getElementById('editMenuItemForm');
    if (editMenuItemForm) {
        editMenuItemForm.addEventListener('submit', function(event) {
            event.preventDefault();
            let id = document.getElementById('menuItemId').value;
            let formData = new FormData(this);
            fetchWithErrorHandling('/admin/nav/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'PUT',
                    'Accept': 'application/json'
                },
                body: formData
            }).then(data => {
                if (data.success) {
                    var editMenuItemModal = bootstrap.Modal.getInstance(document.getElementById('editMenuItemModal'));
                    editMenuItemModal.hide();
                    location.reload();
                }
            });
        });
    }

    let deleteMenuItem = document.getElementById('deleteMenuItem');
    if (deleteMenuItem) {
        deleteMenuItem.addEventListener('click', function() {
            let id = document.getElementById('menuItemId').value;
            fetchWithErrorHandling('/admin/nav/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-HTTP-Method-Override': 'DELETE',
                    'Accept': 'application/json'
                }
            }).then(data => {
                if (data.success) {
                    var editMenuItemModal = bootstrap.Modal.getInstance(document.getElementById('editMenuItemModal'));
                    editMenuItemModal.hide();
                    location.reload();
                }
            });
        });
    }

    let addMenuItemForm = document.getElementById('addMenuItemForm');
    if (addMenuItemForm) {
        addMenuItemForm.addEventListener('submit', function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            fetchWithErrorHandling('/admin/nav', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            }).then(data => {
                if (data.success) {
                    var addMenuItemModal = bootstrap.Modal.getInstance(document.getElementById('addMenuItemModal'));
                    addMenuItemModal.hide();
                    location.reload();
                } else {
                    alert('An error occurred: ' + data.message);
                }
            });
        });
    }

    let addDropdownMenuForm = document.getElementById('addDropdownMenuForm');
        if (addDropdownMenuForm) {
            addDropdownMenuForm.addEventListener('submit', function(event) {
                event.preventDefault();
                let formData = new FormData(this);
                
                // Add a hidden URL field with value '#' for dropdown menus
                formData.append('url', '#');

                fetchWithErrorHandling('/admin/nav', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                }).then(data => {
                    if (data.success) {
                        var addDropdownMenuModal = bootstrap.Modal.getInstance(document.getElementById('addDropdownMenuModal'));
                        addDropdownMenuModal.hide();
                        location.reload();
                    } else {
                        alert('An error occurred: ' + data.message);
                    }
                });
            });
        }

    let saveOrderButton = document.getElementById('save-order');
    if (saveOrderButton) {
        saveOrderButton.addEventListener('click', function() {
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

            fetchWithErrorHandling('{{ route('admin.menu.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ order: order })
            }).then(data => {
                if (data.success) {
                    alert('Order saved successfully.');
                } else {
                    alert('An error occurred: ' + data.message);
                }
            });
        });
    }

    function fetchWithErrorHandling(url, options) {
        return fetch(url, options)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(JSON.stringify(errorData));
                    });
                }
                return response.json();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
                throw error;
            });
    }
});
</script>
@endsection

