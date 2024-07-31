<!-- File: resources/views/schedules/schedule_modal.blade.php -->

<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">Schedule Wizard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="schedule-form">
                    @csrf
                    <input type="hidden" id="schedule-id" name="schedule_id">
                    <div id="wizard-step-1" class="wizard-step">
                        <h5>Select Trigger</h5>
                        <div class="form-group">
                            <label for="months">Months</label>
                            <div id="months" class="btn-group-toggle" data-toggle="buttons">
                                @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                                    <label class="btn btn-outline-primary">
                                        <input type="checkbox" name="months[]" value="{{ $month }}" autocomplete="off"> {{ $month }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="days">Days</label>
                            <div id="days" class="btn-group-toggle" data-toggle="buttons">
                                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                    <label class="btn btn-outline-primary">
                                        <input type="checkbox" name="days[]" value="{{ $day }}" autocomplete="off"> {{ $day }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="hours">Hours</label>
                            <div id="hours" class="btn-group-toggle" data-toggle="buttons">
                                @for($i = 0; $i < 24; $i++)
                                    <label class="btn btn-outline-primary">
                                        <input type="checkbox" name="hours[]" value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" autocomplete="off"> {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                    </label>
                                @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="minutes">Minutes</label>
                            <div id="minutes" class="btn-group-toggle" data-toggle="buttons">
                                @for($i = 0; $i < 60; $i+=5)
                                    <label class="btn btn-outline-primary">
                                        <input type="checkbox" name="minutes[]" value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" autocomplete="off"> {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                    </label>
                                @endfor
                            </div>
                        </div>
                        <p id="schedule-summary" class="mt-3"></p>
                        <button type="button" class="btn btn-primary" id="next-step">Next</button>
                    </div>
                    <div id="wizard-step-2" class="wizard-step" style="display: none;">
                        <h5>Select Action</h5>
                        <div class="form-group">
                            <label for="action">Action</label>
                            <select name="action" id="action" class="form-control">
                                <option value="start">Start</option>
                                <option value="stop">Stop</option>
                                <option value="restart">Restart</option>
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
                        <button type="button" class="btn btn-secondary" id="previous-step">Previous</button>
                        <button type="submit" class="btn btn-success">Save Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
