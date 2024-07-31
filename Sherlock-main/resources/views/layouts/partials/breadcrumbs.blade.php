@php
use Illuminate\Support\Facades\Route;

$currentPath = request()->path();
$breadcrumbs = collect();

if ($currentPath === 'home' || $currentPath === '/') {
    $breadcrumbs->push(['title' => 'Home', 'url' => url('/home')]);
} else {
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
}

// Replace the last breadcrumb with the pageName if it's set
if (View::hasSection('pageName')) {
    $pageName = View::getSection('pageName');
    $breadcrumbs->pop();
    $breadcrumbs->push([
        'title' => ucfirst($pageName),
        'url' => url()->current(),
    ]);
}
@endphp

@unless($breadcrumbs->isEmpty())
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ $breadcrumbs->last()['title'] }}</h5>
                </div>
                <ul class="breadcrumb">
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if (!$loop->last)
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb['url'] }}">
                                    @if ($loop->first)
                                        <i class="feather icon-home"></i>
                                    @endif
                                    {{ $breadcrumb['title'] }}
                                </a>
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="#!">
                                    @if ($loop->first)
                                        <i class="feather icon-home"></i>
                                    @endif
                                    {{ $breadcrumb['title'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endunless