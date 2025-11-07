@extends('layouts.app')

@section('title', 'Add New Trainer - GymPro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus text-primary me-2"></i>
            Add New Trainer
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('gym.trainers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Trainers
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Trainer Information Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Trainer Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('gym.trainers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Personal Information</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Professional Information</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="specializations" class="form-label">Specializations <span class="text-danger">*</span></label>
                                <select class="form-select" id="specializations" name="specializations[]" multiple required>
                                    <option value="strength_training">Strength Training</option>
                                    <option value="cardio">Cardio Training</option>
                                    <option value="crossfit">CrossFit</option>
                                    <option value="yoga">Yoga</option>
                                    <option value="pilates">Pilates</option>
                                    <option value="nutrition">Nutrition Coaching</option>
                                    <option value="weight_loss">Weight Loss</option>
                                    <option value="bodybuilding">Bodybuilding</option>
                                </select>
                                <div class="form-text">Hold Ctrl/Cmd to select multiple specializations</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="experience_years" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control" id="experience_years" name="experience_years" min="0">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="certifications" class="form-label">Certifications</label>
                                <textarea class="form-control" id="certifications" name="certifications" rows="3" placeholder="List certifications..."></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate ($)</label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" step="0.01" min="0">
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('gym.trainers.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Save Trainer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Trainer Photo -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Trainer Photo</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img id="photo-preview" src="https://via.placeholder.com/150x150" alt="Trainer Photo" class="rounded-circle" width="150" height="150" style="object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <div class="form-text">Upload trainer photo (optional)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection