@extends('layouts.app')

@section('content')
    <div class="page-header d-flex justify-content-between">
        <h1 class="page-title">
            <i class="fas fa-users me-2"></i>
            Manage Contacts
        </h1>
        <button class="btn btn-primary" onclick="openAddContactModal()">
            <i class="fas fa-user-plus me-1"></i> Add Contact
        </button>
    </div>

    <!-- Filters Card -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Contacts</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="search_name" class="form-control border-start-0" placeholder="Search by Name">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-envelope text-muted"></i>
                    </span>
                    <input type="text" id="search_email" class="form-control border-start-0" placeholder="Search by Email">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-venus-mars text-muted"></i>
                    </span>
                    <select id="search_gender" class="form-select border-start-0">
                        <option value="">All Genders</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary w-100" id="clear_filters">
                    <i class="fas fa-times-circle me-1"></i> Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Contacts Table -->
    @include('contacts.partials.contacts_table', ['contacts' => $contacts])

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="contactForm" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contactModalTitle">
                            <i class="fas fa-user-plus me-2"></i>Add Contact
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="contact_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <input type="text" name="name" class="form-control" placeholder="Full Name">
                                </div>
                                <span class="text-danger error-text name_error small"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control" placeholder="Email Address">
                                </div>
                                <span class="text-danger error-text email_error small"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-phone text-muted"></i>
                                    </span>
                                    <input type="text" name="phone" class="form-control" placeholder="Phone Number">
                                </div>
                                <span class="text-danger error-text phone_error small"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-venus-mars text-muted"></i>
                                    </span>
                                    <select name="gender" class="form-select">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <span class="text-danger error-text gender_error small"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Profile Image</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-image text-muted"></i>
                                    </span>
                                    <input type="file" name="profile_image" class="form-control">
                                </div>
                                <span class="text-danger error-text profile_image_error small"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Additional File</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-file text-muted"></i>
                                    </span>
                                    <input type="file" name="additional_file" class="form-control">
                                </div>
                                <span class="text-danger error-text additional_file_error small"></span>
                            </div>
                        </div>
                        @if($customFields->count() > 0)
                        <div id="custom_fields_area" class="mt-4">
                            <h6 class="mb-3 border-bottom pb-2">
                                <i class="fas fa-list-alt me-2"></i>Custom Fields
                            </h6>
                            <div class="row g-3">
                                @foreach ($customFields as $field)
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">{{ $field->name }}</label>
                                        @if ($field->type === 'text')
                                            <input type="text" class="form-control"
                                                name="custom_fields[{{ $field->id }}]">
                                            <span
                                                class="text-danger error-text custom_fields_{{ $field->id }}_error small"></span>
                                        @elseif($field->type === 'name')
                                            <input type="name" class="form-control"
                                                name="custom_fields[{{ $field->id }}]">
                                            <span
                                                class="text-danger error-text custom_fields_{{ $field->id }}_error small"></span>
                                        @elseif($field->type === 'date')
                                            <input type="date" class="form-control"
                                                name="custom_fields[{{ $field->id }}]">
                                            <span
                                                class="text-danger error-text custom_fields_{{ $field->id }}_error small"></span>
                                        @elseif($field->type === 'textarea')
                                            <textarea class="form-control" name="custom_fields[{{ $field->id }}]"></textarea>
                                            <span
                                                class="text-danger error-text custom_fields_{{ $field->id }}_error small"></span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Contact
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Merge Modal --}}
    <div class="modal fade" id="mergeContactsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-merge me-2"></i>Merge Contacts
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="mergeContactsForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="master_contact_id" class="form-label fw-semibold">Master Contact (will be kept)</label>
                            <select name="master_contact_id" id="master_contact_id" class="form-control" required>
                                <option value="">Select Master Contact</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="merged_contact_id" class="form-label fw-semibold">Contact to Merge (will be deactivated)</label>
                            <select name="merged_contact_id" id="merged_contact_id" class="form-control" required>
                                <option value="">Select Contact to Merge</option>
                            </select>
                        </div>
                        <div id="mergePreview" class="mt-3 d-none">
                            <h5 class="mb-3">
                                <i class="fas fa-eye me-2"></i>Merge Preview
                            </h5>
                            <div id="mergePreviewContent"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-merge me-1"></i> Confirm Merge
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Your existing JavaScript code remains unchanged
        $(document).ready(function() {
            // live filter handlers
            $('#search_name, #search_email').on('keyup', debounce(fetchContacts, 500));
            $('#search_gender').on('change', fetchContacts);

            // Clear filter
            $('#clear_filters').on('click', function() {
                $('#search_name').val('');
                $('#search_email').val('');
                $('#search_gender').val('');
                fetchContacts();
            });

            // Add/Edit Contact Form Submission
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    url: "{{ route('contacts.store') }}",
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        $('#contactModal').modal('hide');
                        fetchContacts();
                        Swal.fire('Success', res.message, 'success');
                        $('#contactForm')[0].reset();
                        $('.error-text').text('');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                $('.' + key.replace(/\./g, '_') + '_error').text(val[0]);
                            });
                        } else {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    }
                });
            });


            // Clear form when modal is hidden
            $('#mergeContactsModal').on('hidden.bs.modal', function() {
                $('#master_contact_id').html('<option value="">Select Master Contact</option>').prop('disabled', false);
                $('#merged_contact_id').html('<option value="">Select Contact to Merge</option>').prop('disabled', false);
                $('#mergePreview').addClass('d-none');
            });

            // Show preview when both contacts are selected
            $('#master_contact_id').change(function() {
                const masterId = $(this).val();
                const mergedId = $('#merged_contact_id').val();
                
                if (masterId && mergedId) {
                    showMergePreview(masterId, mergedId);
                } else {
                    $('#mergePreview').addClass('d-none');
                }
            });

            // Handle merge form submission
            $('#mergeContactsForm').on('submit', function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                
                submitBtn.prop('disabled', false).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Merging...'
                );
                
                $.ajax({
                    url: "{{ route('contacts.merge') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#mergeContactsModal').modal('hide');
                            fetchContacts(); // Refresh the contacts table
                            submitBtn.prop('disabled', false).html('Confirm Merge');
                        } 
                        submitBtn.prop('disabled', false).html('Confirm Merge');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error('An error occurred during merging');
                        }
                        submitBtn.prop('disabled', false).html("Confirm Merge");
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('Confirm Merge');
                    }
                });
            });

        });

        $(document).on('click', '.edit-contact', function(){
            // Edit 
            var contactId = $(this).data('id');
            $.get(`/contacts/${contactId}`, function(res) {
                const c = res.data;

                $('#contactModalTitle').text('Edit Contact');
                $('.error-text').text('');

                $('#contact_id').val(c.id);
                $('[name="name"]').val(c.name);
                $('[name="email"]').val(c.email);
                $('[name="phone"]').val(c.phone);
                $('[name="gender"]').val(c.gender);

                // Clear and reload custom fields
                @foreach ($customFields as $field)
                    @if ($field->type === 'text' || $field->type === 'date')
                        $(`[name="custom_fields[{{ $field->id }}]"]`).val('');
                    @elseif ($field->type === 'textarea')
                        $(`[name="custom_fields[{{ $field->id }}]"]`).text('');
                    @endif
                @endforeach

                if (c.custom_fields.length) {
                    c.custom_fields.forEach(function(cf) {
                        $(`[name="custom_fields[${cf.id}]"]`).val(cf.value);
                    });
                }

                $('#contactModal').modal('show');
            });
        });

        $(document).on('click','.deleteForm', function(e){
            // Delete
            e.preventDefault();
            let id = $(this).data('id');
            let form = this;

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this contact.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/contacts/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: res => {
                            Swal.fire('Deleted', res.message, 'success');
                            fetchContacts();
                        }
                    });
                }
            });
        });

        // Handle merge button click
        $(document).on('click', '.merge-contacts', function() {
            const contactId = $(this).data('id');
            const contactName = $(this).closest('tr').find('td:first').text().trim();
            
            // Set the contact to be merged
            $('#merged_contact_id').html(`
                <option value="${contactId}" selected>${contactName}</option>
            `);
            
            // Clear and disable master contact select until loaded
            $('#master_contact_id').html('<option value="">Loading contacts...</option>').prop('disabled', true);
            
            // Show the modal
            $('#mergeContactsModal').modal('show');
            
            // Load available contacts for master selection
            loadContactsForMerge(contactId);
        });

        // Function to load contacts for merging
        function loadContactsForMerge(excludeId) {
            $('#master_contact_id').html('<option value="">Loading contacts...</option>');
                $.ajax({
                url: "{{ route('contactsfetch') }}",
                type: "GET",
                data: {
                    exclude: excludeId // Pass the contact ID to exclude
                },
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">Select Master Contact</option>';
                        
                        if (response.contacts && response.contacts.length > 0) {
                            response.contacts.forEach(contact => {
                                options += `<option value="${contact.id}">${contact.text}</option>`;
                            });
                        } else {
                            options = '<option value="">No contacts available</option>';
                            toastr.warning('No other contacts available for merging');
                        }
                        
                        $('#master_contact_id').html(options).prop('disabled', false);
                    } else {
                        $('#master_contact_id').html('<option value="">Error: ' + (response.message || 'Unknown error') + '</option>');
                        toastr.error(response.message || 'Failed to load contacts');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    $('#master_contact_id').html('<option value="">Error loading contacts</option>');
                    toastr.error('Server error occurred while loading contacts');
                }
            });
        }

         // Function to show merge preview
        function showMergePreview(masterId, mergedId) {
            $.get("{{ route('contacts.merge.preview') }}", {
                master_id: masterId,
                merged_id: mergedId
            }, function(data) {
                $('#mergePreviewContent').html(data);
                $('#mergePreview').removeClass('d-none');
            }).fail(function() {
                toastr.error('Failed to load merge preview');
                $('#mergePreview').addClass('d-none');
            });
        }

        // Debounce function to limit frequent requests while typing
        function debounce(func, delay) {
            let timeout;
            return function() {
                clearTimeout(timeout);
                timeout = setTimeout(func, delay);
            };
        }

        function openAddContactModal() {
            $('#contactModalTitle').text('Add Contact');
            $('#contactForm')[0].reset(); // Reset the entire form
            $('#contact_id').val(''); // Clear the hidden ID field
            $('.error-text').text(''); // Clear validation errors if needed
            // Open the modal manually
            const modal = new bootstrap.Modal(document.getElementById('contactModal'));
            modal.show();
        }

        // Get all recode
        function fetchContacts() {
            $.get("{{ route('contacts.index') }}", {
                _token: "{{ csrf_token() }}",
                name: $('#search_name').val(),
                email: $('#search_email').val(),
                gender: $('#search_gender').val()
            }, function(res) {
                $('#contactsTable').html($(res).find('#contactsTable').html());
                // Update the pagination links
                $('.pagination').html($(res).find('.pagination').html());
            });
        }
    </script>
@endsection