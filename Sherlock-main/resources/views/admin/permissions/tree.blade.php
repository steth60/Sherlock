@foreach($permissions as $permission)
    <li class="list-group-item">
        <div class="d-flex justify-content-between align-items-center">
            <span>
                @if($permission->children->isNotEmpty())
                    <i class="fas fa-caret-right toggle-children" style="cursor: pointer;"></i>
                @else
                    <i class="fas fa-circle" style="font-size: 0.5em; vertical-align: middle;"></i>
                @endif
                {{ $permission->name }}
            </span>
            <div class="btn-group">
                <button class="btn btn-sm btn-primary" onclick="showEditModal('{{ $permission->id }}', '{{ $permission->name }}', '{{ $permission->parent_id }}')">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="showDeleteModal('{{ $permission->id }}')">Delete</button>
            </div>
        </div>
        @if($permission->children->isNotEmpty())
            <ul class="list-group mt-2" style="display: none;">
                @include('admin.permissions.tree', ['permissions' => $permission->children])
            </ul>
        @endif
    </li>
@endforeach