@extends('layouts.app')

@section('content')
    <h1>Edit Schedule for {{ $schedule->instance->name }}</h1>

    <form action="{{ route('schedules.update', $schedule) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="action">Action</label>
            <select name="action" id="action" class="form-control">
                <option value="start" {{ $schedule->action === 'start' ? 'selected' : '' }}>Start</option>
                <option value="stop" {{ $schedule->action === 'stop' ? 'selected' : '' }}>Stop</option>
                <option value="restart" {{ $schedule->action === 'restart' ? 'selected' : '' }}>Restart</option>
            </select>
        </div>
        <div class="form-group">
            <label for="months">Months</label>
            <select name="months[]" id="months" class="form-control" multiple>
                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                    <option value="{{ $month }}" {{ in_array($month, $schedule->months) ? 'selected' : '' }}>{{ $month }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="days">Days</label>
            <select name="days[]" id="days" class="form-control" multiple>
                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <option value="{{ $day }}" {{ in_array($day, $schedule->days) ? 'selected' : '' }}>{{ $day }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="hours">Hours</label>
            <select name="hours[]" id="hours" class="form-control" multiple>
                @for($i = 0; $i < 24; $i++)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ in_array(str_pad($i, 2, '0', STR_PAD_LEFT), $schedule->hours) ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label for="minutes">Minutes</label>
            <select name="minutes[]" id="minutes" class="form-control" multiple>
                @for($i = 0; $i < 60; $i+=5)
                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ in_array(str_pad($i, 2, '0', STR_PAD_LEFT), $schedule->minutes) ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" id="description" class="form-control" value="{{ $schedule->description }}">
        </div>
        <div class="form-group">
            <label for="enabled">Enabled</label>
            <input type="checkbox" name="enabled" id="enabled" value="1" {{ $schedule->enabled ? 'checked' : '' }}>
        </div>
        <button type="submit" class="btn btn-primary">Update Schedule</button>
    </form>
@endsection