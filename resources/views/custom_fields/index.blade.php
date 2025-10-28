@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header d-flex justify-content-between mb-4">
            <h1 class="page-title">
                <i class="fas fa-cogs me-2"></i>
                Manage Custom Fields
            </h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customFieldModal">
                <i class="fas fa-plus-circle me-1"></i> Add Field
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Custom Fields
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="custom-fields-table">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Show on Table</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="customFieldModal" tabindex="-1" aria-labelledby="customFieldModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form id="custom-field-form" class="modal-content">
                    @csrf
                    <input type="hidden" name="id" id="field-id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customFieldModalLabel">
                            <i class="fas fa-plus-circle me-2"></i>Add Custom Field
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Field Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-tag text-muted"></i>
                                </span>
                                <input type="text" name="name" class="form-control" placeholder="Enter field name">
                            </div>
                            <span class="text-danger error-text name_error small"></span>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Field Type</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-list text-muted"></i>
                                </span>
                                <select name="type" class="form-select">
                                    <option value="">Select Type</option>
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="date">Date</option>
                                    <option value="number">Number</option>
                                </select>
                            </div>
                            <span class="text-danger error-text type_error small"></span>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_required" id="is_required"
                                    value="true">
                                <label class="form-check-label fw-semibold" for="is_required">
                                    <i class="fas fa-asterisk text-danger me-1 small"></i>Required
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_on_table" id="show_on_table"
                                    value="true">
                                <label class="form-check-label fw-semibold" for="show_on_table">
                                    <i class="fas fa-table me-1 small"></i>Show on Table
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Field
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            loadCustomFields();
        });
        const modal = new bootstrap.Modal(document.getElementById('customFieldModal'));

        function loadCustomFields() {
            $.get("{{ route('custom-fields.fetch') }}", function(res) {
                if (res.status === 'success') {
                    let rows = '';
                    if (res.fields.length > 0) {
                        res.fields.forEach(field => {
                            rows += `
                                <tr id="field-${field.id}">
                                    <td class="fw-semibold">${field.name}</td>
                                    <td>
                                        <span class="badge bg-primary">${field.type.charAt(0).toUpperCase() + field.type.slice(1)}</span>
                                    </td>
                                    <td>
                                        ${field.is_required ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}
                                    </td>
                                    <td>
                                        ${field.show_on_table ? '<span class="badge bg-info">Yes</span>' : '<span class="badge bg-secondary">No</span>'}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-warning edit-field" data-id="${field.id}"
                                                data-field='${JSON.stringify(field).replace(/'/g, "&apos;")}'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-field" data-id="${field.id}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        rows = `
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                                    No custom fields found
                                </td>
                            </tr>`;
                    }
                    $('#custom-fields-table tbody').html(rows);
                }
            });
        }

        $('#custom-field-form').submit(function(e) {
            e.preventDefault();
            $('.error-text').text('');

            const form = $(this);
            const formData = form.serialize();
            const url = "{{ route('custom-fields.store') }}";
            const method = "POST";

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(res) {
                    if (res.status === 'success') {
                        loadCustomFields();
                    }
                    modal.hide();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            $('.' + key + '_error').text(val[0]);
                        });
                    } else {
                        alert('Something went wrong.');
                    }
                }
            });
        });

        $(document).on('click', '.delete-field', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let form = this;

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this custom field.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/custom-fields/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: res => {
                            Swal.fire('Deleted', res.message, 'success');
                            loadCustomFields();
                        },
                        error: function() {
                            Swal.fire('Failed to delete the field');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.edit-field', function() {
            const field = $(this).data('field');
            $('#field-id').val(field.id);
            $('[name="name"]').val(field.name);
            $('[name="type"]').val(field.type);
            $('#is_required').prop('checked', field.is_required == 1);
            $('#show_on_table').prop('checked', field.show_on_table == 1);
            $('#customFieldModalLabel').html('<i class="fas fa-edit me-2"></i>Edit Custom Field');
            modal.show();
        });

        $('#customFieldModal').on('hidden.bs.modal', function() {
            $('#custom-field-form')[0].reset();
            $('#field-id').val('');
            $('.error-text').text('');
            $('#customFieldModalLabel').html('<i class="fas fa-plus-circle me-2"></i>Add Custom Field');
        });
    </script>
@endsection