<div class="accordion-item">
    <h2 class="accordion-header" id="heading{{ $permission->id }}">
        <button class="accordion-button @if($permission->children->isEmpty()) no-toggle @else collapsed @endif" type="button"
                @if($permission->children->isNotEmpty())
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapse{{ $permission->id }}" 
                    aria-expanded="false" 
                    aria-controls="collapse{{ $permission->id }}"
                @endif>
            {{ $permission->name }}
        </button>
    </h2>
    <div class="accordion-actions">
        <button class="btn btn-sm btn-primary" onclick="showEditModal('{{ $permission->id }}', '{{ $permission->name }}', '{{ $permission->parent_id }}')">Edit</button>
        <button class="btn btn-sm btn-danger" onclick="showDeleteModal('{{ $permission->id }}')">Delete</button>
    </div>
    @if($permission->children->isNotEmpty())
        <div id="collapse{{ $permission->id }}" class="accordion-collapse collapse" 
             aria-labelledby="heading{{ $permission->id }}" data-bs-parent="#permissionsAccordion">
            <div class="accordion-body">
                <ul class="list-group">
                    @foreach($permission->children as $childPermission)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-circle" style="font-size: 0.5em; vertical-align: middle;"></i>
                                    {{ $childPermission->name }}
                                </span>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary" onclick="showEditModal('{{ $childPermission->id }}', '{{ $childPermission->name }}', '{{ $childPermission->parent_id }}')">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="showDeleteModal('{{ $childPermission->id }}')">Delete</button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>