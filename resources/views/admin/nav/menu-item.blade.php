<div class="card mb-2" data-id="{{ $menuItem->id }}">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="handle mr-3" style="cursor: move;">&#9776;</span>
                {{ $menuItem->title }}
            </div>
            <div>
                <button class="btn btn-sm btn-primary edit-menu-item" data-menu-item="{{ json_encode($menuItem) }}">Edit</button>
            </div>
        </div>
        @if($menuItem->children->count())
            <div class="ml-4">
                @foreach($menuItem->children as $child)
                    @include('admin.nav.menu-item', ['menuItem' => $child, 'level' => $level + 1])
                @endforeach
            </div>
        @endif
    </div>
</div>
