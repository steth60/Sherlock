@php
use Illuminate\Support\Facades\Route;

$breadcrumbs = collect();

// Check if 'home' route exists, if not, use '/' as fallback
if (Route::has('home')) {
    $breadcrumbs->push(['title' => 'Home', 'url' => route('home')]);
} else {
    $breadcrumbs->push(['title' => 'Home', 'url' => url('/')]);
}

$segments = collect(request()->segments());

$segments->each(function ($segment, $key) use (&$breadcrumbs, $segments) {
    $url = '/' . $segments->slice(0, $key + 1)->join('/');
    $route = Route::getRoutes()->match(request()->create($url));
    $name = $route->getName();

    if ($name) {
        $breadcrumbs->push([
            'title' => ucfirst($segment),
            'url' => $url,
        ]);
    }
});
@endphp

@unless($breadcrumbs->isEmpty())
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach ($breadcrumbs as $breadcrumb)
                @if (!$loop->last)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
                @endif
            @endforeach
        </ol>
    </nav>
@endunless