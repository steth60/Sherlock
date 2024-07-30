<div class="permission-item" style="margin-left: {{ $depth * 20 }}px;">
    <div class="form-check">
        <input class="form-check-input @if($permission->children->isNotEmpty()) parent-permission-checkbox @else child-permission-checkbox @endif"
               type="checkbox"
               name="permissions[]"
               value="{{ $permission->id }}"
               id="permission{{ $group->id }}-{{ $permission->id }}"
               {{ $group->permissions->contains($permission->id) ? 'checked' : '' }}>
        <label class="form-check-label" for="permission{{ $group->id }}-{{ $permission->id }}">
            @if($permission->children->isNotEmpty())
                <i class="fas fa-caret-right toggle-children" style="cursor: pointer;"></i>
            @endif
            {{ $permission->name }}
        </label>
    </div>
</div>
@if($permission->children->isNotEmpty() || $group->permissions->contains($permission->id))
    <div class="children @if(!$group->permissions->contains($permission->id)) d-none @endif" style="margin-left: {{ ($depth + 1) * 20 }}px;">
        @foreach($permission->children as $childPermission)
            @include('admin.groups.permission_tree_item', ['permission' => $childPermission, 'group' => $group, 'depth' => $depth + 1])
        @endforeach
    </div>
@endif
