@extends('layouts.app')

@section('title', 'Add New Member - GymPro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus text-primary me-2"></i>
            Add New Member
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('gym.members.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Members
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Member Information Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Member Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('gym.members.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Personal Information</h6>
                            </div>
                            <!-- Member ID (scanned or manual) -->
                            <div class="col-md-6 mb-3">
                                <label for="member_id" class="form-label">Member ID (Scan or Enter) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="member_id" name="member_id" placeholder="Scan or enter member ID" required>
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
                            
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Emergency Contact</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                                <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" placeholder="e.g., Spouse, Parent, Friend">
                            </div>
                        </div>

                        <!-- Health Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Health Information</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="medical_conditions" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="medical_conditions" name="medical_conditions" rows="3" placeholder="Any medical conditions or allergies..."></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="fitness_goals" class="form-label">Fitness Goals</label>
                                <textarea class="form-control" id="fitness_goals" name="fitness_goals" rows="3" placeholder="Weight loss, muscle gain, general fitness..."></textarea>
                            </div>
                        </div>

                        <!-- Membership Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Membership Details</h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="membership_type" class="form-label">Membership Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="membership_type" name="membership_type" required>
                                    <option value="">Select Membership Type</option>
                                    <option value="basic_monthly">Basic Monthly - $30/month</option>
                                    <option value="premium_monthly">Premium Monthly - $50/month</option>
                                    <option value="basic_quarterly">Basic Quarterly - $80/3 months</option>
                                    <option value="premium_quarterly">Premium Quarterly - $140/3 months</option>
                                    <option value="basic_annual">Basic Annual - $300/year</option>
                                    <option value="premium_annual">Premium Annual - $500/year</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Membership Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="member_status" class="form-label">Status</label>
                                <select class="form-select" id="member_status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">Additional Information</h6>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes about the member..."></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('gym.members.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Save Member
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Member Photo -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Member Photo</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img id="photo-preview" src="https://via.placeholder.com/150x150" alt="Member Photo" class="rounded-circle" width="150" height="150" style="object-fit: cover;">
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <div class="form-text">Upload member photo (optional)</div>
                    </div>
                </div>
            </div>

            <!-- Membership Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Info</h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-2">Membership Benefits:</div>
                    <ul class="list-unstyled mb-0" id="membership-benefits">
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Gym Access</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Locker Room</li>
                        <li class="mb-1"><i class="fas fa-times text-muted me-2"></i>Personal Training</li>
                        <li class="mb-1"><i class="fas fa-times text-muted me-2"></i>Group Classes</li>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="generateMemberID()">
                            <i class="fas fa-id-card me-2"></i>
                            Generate Member ID
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="checkEmailAvailability()">
                            <i class="fas fa-envelope me-2"></i>
                            Check Email
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="calculateMembershipEnd()">
                            <i class="fas fa-calendar me-2"></i>
                            Calculate End Date
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.border-bottom {
    border-color: #e3e6f0 !important;
}

.form-label {
    font-weight: 600;
    color: #5a5c69;
}

.card-header h6 {
    color: #5a5c69;
}

#photo-preview {
    border: 3px solid #e3e6f0;
    transition: all 0.3s ease;
}

#photo-preview:hover {
    border-color: #4e73df;
}

@media (max-width: 768px) {
    .d-sm-flex {
        flex-direction: column;
        align-items: stretch !important;
    }
    
    .d-sm-flex .btn {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Photo preview functionality
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photo-preview');
    
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Membership type change handler
    const membershipSelect = document.getElementById('membership_type');
    const benefitsList = document.getElementById('membership-benefits');
    
    membershipSelect.addEventListener('change', function() {
        updateMembershipBenefits(this.value);
    });
    
    // Start date change handler for end date calculation
    const startDateInput = document.getElementById('start_date');
    const membershipTypeInput = document.getElementById('membership_type');
    
    function calculateEndDate() {
        const startDate = startDateInput.value;
        const membershipType = membershipTypeInput.value;
        
        if (startDate && membershipType) {
            // Calculate end date based on membership type
            const start = new Date(startDate);
            let endDate = new Date(start);
            
            if (membershipType.includes('monthly')) {
                endDate.setMonth(endDate.getMonth() + 1);
            } else if (membershipType.includes('quarterly')) {
                endDate.setMonth(endDate.getMonth() + 3);
            } else if (membershipType.includes('annual')) {
                endDate.setFullYear(endDate.getFullYear() + 1);
            }
            
            console.log('Membership expires on:', endDate.toDateString());
        }
    }
    
    startDateInput.addEventListener('change', calculateEndDate);
    membershipTypeInput.addEventListener('change', calculateEndDate);
});

function updateMembershipBenefits(membershipType) {
    const benefitsList = document.getElementById('membership-benefits');
    const benefits = {
        basic_monthly: ['Gym Access', 'Locker Room'],
        premium_monthly: ['Gym Access', 'Locker Room', 'Group Classes', '2 Personal Training Sessions'],
        basic_quarterly: ['Gym Access', 'Locker Room', 'Group Classes'],
        premium_quarterly: ['Gym Access', 'Locker Room', 'Group Classes', '6 Personal Training Sessions', 'Nutrition Consultation'],
        basic_annual: ['Gym Access', 'Locker Room', 'Group Classes', 'Guest Pass (2/month)'],
        premium_annual: ['Gym Access', 'Locker Room', 'Group Classes', '24 Personal Training Sessions', 'Nutrition Consultation', 'Guest Pass (5/month)', 'Towel Service']
    };
    
    const selectedBenefits = benefits[membershipType] || ['Gym Access', 'Locker Room'];
    const allBenefits = ['Gym Access', 'Locker Room', 'Group Classes', 'Personal Training Sessions', 'Nutrition Consultation', 'Guest Pass', 'Towel Service'];
    
    let html = '';
    allBenefits.forEach(benefit => {
        const included = selectedBenefits.some(sb => sb.includes(benefit));
        const icon = included ? 'fas fa-check text-success' : 'fas fa-times text-muted';
        html += `<li class="mb-1"><i class="${icon} me-2"></i>${benefit}</li>`;
    });
    
    benefitsList.innerHTML = html;
}

function generateMemberID() {
    const timestamp = Date.now();
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const memberID = `GYM${timestamp.toString().slice(-6)}${random}`;
    
    // You can set this to a hidden input or display it somewhere
    console.log('Generated Member ID:', memberID);
    alert('Generated Member ID: ' + memberID);
}

function checkEmailAvailability() {
    const email = document.getElementById('email').value;
    if (!email) {
        alert('Please enter an email address first.');
        return;
    }
    
    // Simulate email check (replace with actual AJAX call)
    console.log('Checking email availability for:', email);
    alert('Email availability check feature will be implemented.');
}

function calculateMembershipEnd() {
    const startDate = document.getElementById('start_date').value;
    const membershipType = document.getElementById('membership_type').value;
    
    if (!startDate || !membershipType) {
        alert('Please select start date and membership type first.');
        return;
    }
    
    const start = new Date(startDate);
    let endDate = new Date(start);
    
    if (membershipType.includes('monthly')) {
        endDate.setMonth(endDate.getMonth() + 1);
    } else if (membershipType.includes('quarterly')) {
        endDate.setMonth(endDate.getMonth() + 3);
    } else if (membershipType.includes('annual')) {
        endDate.setFullYear(endDate.getFullYear() + 1);
    }
    
    alert(`Membership will expire on: ${endDate.toDateString()}`);
}
</script>
@endpush
@endsection