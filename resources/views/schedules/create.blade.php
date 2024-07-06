@extends('layouts.app')

@section('content')
    <h1>Create Schedule for {{ $instance->name }}</h1>

    <form action="{{ route('schedules.store', $instance) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="action">Action</label>
            <select name="action" id="action" class="form-control">
                <option value="start">Start</option>
                <option value="stop">Stop</option>
                <option value="restart">Restart</option>
            </select>
        </div>
        <div class="form-group">
            <label for="months">Months</label>
            <select name="months[]" id="months" class="form-control" multiple>
                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                    <option value="{{ $month }}">{{ $month }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="days">Days</label>
            <select name="days[]" id="days" class="form-control" multiple>
                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <option value="{{ $day }}">{{ $day }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="hours">Hours</label>
            <select name="hours[]" id="hours" class="form-control" multiple>
                @for($i = 0; $i < 24; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label for="minutes">Minutes</label>
            <select name="minutes[]" id="minutes" class="form-control" multiple>
                @for($i = 0; $i < 60; $i+=5)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" id="description" class="form-control">
        </div>
        <div class="form-group">
            <label for="enabled">Enabled</label>
            <input type="checkbox" name="enabled" id="enabled" value="1" checked>
        </div>
        <button type="submit" class="btn btn-primary">Create Schedule</button>
    </form>
@endsection

