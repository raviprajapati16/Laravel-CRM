@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-4">Manage Contacts</h4>
        <button class="btn btn-success" onclick="openAddContactModal()">
            <i class="bi bi-person-plus-fill me-1"></i> Add Contact
        </button>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" id="search_name" class="form-control" placeholder="Search by Name">
                </div>
                <div class="col-md-3">
                    <input type="text" id="search_email" class="form-control" placeholder="Search by Email">
                </div>
                <div class="col-md-3">
                    <select id="search_gender" class="form-select">
                        <option value="">All Genders</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary w-100" id="clear_filters">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </button>
                </div>
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
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="contactModalTitle">Add</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="contact_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Name">
                                <span class="text-danger error-text name_error"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="Phone">
                                <span class="text-danger error-text phone_error"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                <span class="text-danger error-text gender_error"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" class="form-control">
                                <span class="text-danger error-text profile_image_error"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Additional File</label>
                                <input type="file" name="additional_file" class="form-control">
                                <span class="text-danger error-text additional_file_error"></span>
                            </div>
                        </div>

                        <div id="custom_fields_area" class="mt-3">
                            <div class="row g-3">
                                @foreach ($customFields as $field)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $field->name }}</label>
                                        @if ($field->type === 'text')
                                            <input type="text" class="form-control"
                                                name="custom_fields[{{ $field->id }}]">
                                            <span
                                                class="text-danger error-text custom_fields_{{ $field->id }}_error"></span>
                                        @elseif($field->type === 'name')
                                            <input type="name" class="form-control"
                                                name="custom_fields[{{ $field->id }}]">
                                            <span
                                                class="text-danger error-text custom_fields_{{ $field->id }}_error"></span>
                                        @elseif($field->type === 'date')
                                            <input type="date" class="form-control"
                                                name="custom_fields[{{ $field->id }}]">
                                            <span
                                                class="text-danger error-text custom_fields_{{ $field->id }}_error"></span>
                                        @elseif($field->type === 'textarea')
                                            <textarea class="form-control" name="custom_fields[{{ $field->id }}]"></textarea>
                                            <span
                                                class="text-danger error-text custom_fields_{{ $field->id }}_error"></span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Contact
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
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Merge Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="mergeContactsForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="master_contact_id">Master Contact (will be kept)</label>
                            <select name="master_contact_id" id="master_contact_id" class="form-control" required>
                                <option value="">Select Master Contact</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="merged_contact_id">Contact to Merge (will be deactivated)</label>
                            <select name="merged_contact_id" id="merged_contact_id" class="form-control" required>
                                <option value="">Select Contact to Merge</option>
                            </select>
                        </div>
                        <div id="mergePreview" class="mt-3 d-none">
                            <h5>Merge Preview</h5>
                            <div id="mergePreviewContent"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Confirm Merge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            // live filter handlers
            $('#search_name, #search_email').on('keyup', debounce(fetchContacts, 500));
            $('#search_gender').on('change', function() {
                fetchContacts();
            });

            // Clear button handler
            $('#clear_filters').on('click', function() {
                $('#search_name').val('');
                $('#search_email').val('');
                $('#search_gender').val('');
                fetchContacts();
            });

            // Pagination click handler
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const page = $(this).attr('href').split('page=')[1];
                fetchContacts(page);
            });

            // Add and Update
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
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                $('.' + key.replace(/\./g, '_') + '_error').text(val[0]);
                            });
                        } else {
                            alert('Something went wrong!');
                        }
                    }
                });
            });

            // Delete
            $('.deleteForm').on('click', function(e) {
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

            // Edit 
            $('.edit-contact').on('click', function() {
                var contactId = $(this).data('id');
                $.get(`/contacts/${contactId}`, function(res) {
                    const c = res.contact;

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
                            $(`[name="custom_fields[${cf.id}]"]`).val(cf.pivot.value);
                        });
                    }

                    $('#contactModal').modal('show');
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
                alert('mergeContactsForm');
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

  

        // Call on load
        // fetchContacts();
    </script>
@endsection
