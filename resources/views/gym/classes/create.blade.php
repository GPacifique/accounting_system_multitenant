@extends('layouts.app')

@section('title', 'Schedule New Class - GymPro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus text-primary me-2"></i>
            Schedule New Fitness Class
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('gym.fitness-classes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Schedule
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Class Information Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Class Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('gym.fitness-classes.store') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Basic Information</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Class Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="class_type" class="form-label">Class Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="class_type" name="class_type" required>
                                    <option value="">Select Class Type</option>
                                    <option value="yoga">Yoga</option>
                                    <option value="pilates">Pilates</option>
                                    <option value="hiit">HIIT</option>
                                    <option value="strength">Strength Training</option>
                                    <option value="cardio">Cardio</option>
                                    <option value="crossfit">CrossFit</option>
                                    <option value="zumba">Zumba</option>
                                    <option value="spinning">Spinning</option>
                                </select>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Class description and benefits..."></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="difficulty_level" class="form-label">Difficulty Level</label>
                                <select class="form-select" id="difficulty_level" name="difficulty_level">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="all_levels">All Levels</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="trainer_id" class="form-label">Trainer <span class="text-danger">*</span></label>
                                <select class="form-select" id="trainer_id" name="trainer_id" required>
                                    <option value="">Select Trainer</option>
                                    <option value="1">Sarah Johnson - Yoga Specialist</option>
                                    <option value="2">Mike Davis - HIIT & Strength</option>
                                    <option value="3">Lisa Wilson - Pilates & Dance</option>
                                    <option value="4">John Smith - CrossFit Coach</option>
                                </select>
                            </div>
                        </div>

                        <!-- Schedule Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Schedule Information</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="class_date" class="form-label">Class Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="class_date" name="class_date" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="recurring_schedule" class="form-label">Recurring Schedule</label>
                                <select class="form-select" id="recurring_schedule" name="recurring_schedule">
                                    <option value="">One-time Class</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" readonly>
                            </div>
                        </div>

                        <!-- Capacity and Location -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Capacity & Location</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="max_capacity" class="form-label">Maximum Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="max_capacity" name="max_capacity" min="1" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location/Room</label>
                                <select class="form-select" id="location" name="location">
                                    <option value="">Select Location</option>
                                    <option value="studio_a">Studio A</option>
                                    <option value="studio_b">Studio B</option>
                                    <option value="main_gym">Main Gym Floor</option>
                                    <option value="outdoor">Outdoor Area</option>
                                    <option value="pool_area">Pool Area</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="price_per_session" class="form-label">Price per Session ($)</label>
                                <input type="number" class="form-control" id="price_per_session" name="price_per_session" step="0.01" min="0">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="equipment_needed" class="form-label">Equipment Needed</label>
                                <input type="text" class="form-control" id="equipment_needed" name="equipment_needed" placeholder="e.g., Yoga mats, dumbbells">
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('gym.fitness-classes.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Schedule Class
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Schedule Preview -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Schedule Preview</h6>
                </div>
                <div class="card-body">
                    <div id="schedule-preview">
                        <div class="text-center text-muted">
                            <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                            <p>Fill in the form to see schedule preview</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Guidelines -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Scheduling Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Allow 15 min between classes</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Check trainer availability</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ensure room capacity</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Consider equipment needs</li>
                        <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Set appropriate pricing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate duration automatically
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const durationInput = document.getElementById('duration_minutes');
    
    function calculateDuration() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (startTime && endTime) {
            const start = new Date(`1970-01-01T${startTime}:00`);
            const end = new Date(`1970-01-01T${endTime}:00`);
            const diffInMinutes = (end - start) / (1000 * 60);
            
            if (diffInMinutes > 0) {
                durationInput.value = diffInMinutes;
            } else {
                durationInput.value = '';
            }
        }
    }
    
    startTimeInput.addEventListener('change', calculateDuration);
    endTimeInput.addEventListener('change', calculateDuration);
    
    // Update schedule preview
    function updateSchedulePreview() {
        const name = document.getElementById('name').value;
        const classType = document.getElementById('class_type').value;
        const date = document.getElementById('class_date').value;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const trainer = document.getElementById('trainer_id').selectedOptions[0]?.text;
        const capacity = document.getElementById('max_capacity').value;
        const location = document.getElementById('location').value;
        
        if (name || classType || date || startTime) {
            const preview = document.getElementById('schedule-preview');
            preview.innerHTML = `
                <div class="card border-primary">
                    <div class="card-body">
                        <h6 class="card-title text-primary">${name || 'Class Name'}</h6>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-tag me-1"></i>${classType || 'Type'}<br>
                                <i class="fas fa-calendar me-1"></i>${date || 'Date'}<br>
                                <i class="fas fa-clock me-1"></i>${startTime || 'Start'} - ${endTime || 'End'}<br>
                                <i class="fas fa-user me-1"></i>${trainer || 'Trainer'}<br>
                                <i class="fas fa-users me-1"></i>Max: ${capacity || '0'}<br>
                                <i class="fas fa-map-marker-alt me-1"></i>${location || 'Location'}
                            </small>
                        </p>
                    </div>
                </div>
            `;
        }
    }
    
    // Add event listeners for preview updates
    ['name', 'class_type', 'class_date', 'start_time', 'end_time', 'trainer_id', 'max_capacity', 'location'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', updateSchedulePreview);
        document.getElementById(id)?.addEventListener('change', updateSchedulePreview);
    });
});
</script>
@endpush
@endsection