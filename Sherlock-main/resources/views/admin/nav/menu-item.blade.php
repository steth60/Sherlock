<div class="card mb-2" data-id="{{ $menuItem->id }}">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="handle mr-2" style="cursor: move;">☰</span>
                <span style="margin-left: {{ $depth * 20 }}px;"></span>
                @if($menuItem->icon)
                    <i class="{{ $menuItem->icon }}"></i>
                @endif
                {{ $menuItem->title }}
                @if($menuItem->url != 'javascript:void(0)')
                    <small class="text-muted">({{ $menuItem->url }})</small>
                @endif
            </div>
            <div>
                <button class="btn btn-sm btn-primary edit-menu-item" data-menu-item="{{ json_encode($menuItem) }}">Edit</button>
            </div>
        </div>
    </div>
    @if($menuItem->children->isNotEmpty())
        <div class="card-footer p-0">
            <div class="pl-4">
                @foreach($menuItem->children as $childItem)
                    @include('admin.nav.menu-item', ['menuItem' => $childItem, 'depth' => $depth + 1])
                @endforeach
            </div>
        </div>
    @endif
</div>