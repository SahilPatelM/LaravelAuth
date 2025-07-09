@extends('layouts/contentNavbarLayout')

@section('title', 'Users')

@section('content')
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Users /</span> Users List
    </h4>

    <!-- Hoverable Table rows -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Users List</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-plus"></i> Create User
            </button>
        </div>
        <div class="card-body tablecard">
            <table id="users-table" class="table table-striped table-hover align-middle rounded-3" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--/ Hoverable Table rows -->

    <!-- Create Users Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createUserForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter user name">
                            <div class="invalid-feedback" id="name_error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter user email">
                            <div class="invalid-feedback" id="email_error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Enter password">
                            <div class="invalid-feedback" id="password_error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="user_role" class="form-label">Role *</label>
                            <select class="form-select" id="user_role" name="user_role">
                                <option value="">Select a role</option>
                                <option value="1">Admin</option>
                                <option value="2">Vendor</option>
                                <option value="3">Staff</option>
                                <option value="4">Customer</option>
                            </select>
                            <div class="invalid-feedback" id="user_role_error"></div>
                        </div>

                        <div class="mb-3" id="is_selling_tea_wrapper" style="display: none;">
                            <input type="hidden" name="is_selling_tea" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_selling_tea" name="is_selling_tea"
                                    value="1">
                                <label class="form-check-label" for="is_selling_tea">Is Selling Tea</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        $(document).ready(function() {

            // Initialize DataTable
            $('#users-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('users.list') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'user_role',
                        name: 'user_role',
                        render: function(data, type, row) {
                            var badgeClass = '';
                            var roleText = '';

                            switch (parseInt(data)) {
                                case 1:
                                    badgeClass = 'badge rounded-pill bg-label-primary';
                                    roleText = 'Admin';
                                    break;
                                case 2:
                                    badgeClass = 'badge rounded-pill bg-label-success';
                                    roleText = 'Vendor';
                                    break;
                                case 3:
                                    badgeClass = 'badge rounded-pill bg-label-warning';
                                    roleText = 'Staff';
                                    break;
                                case 4:
                                    badgeClass = 'badge rounded-pill bg-label-info';
                                    roleText = 'Customer';
                                    break;
                                default:
                                    badgeClass = 'badge rounded-pill bg-label-secondary';
                                    roleText = 'Unknown';
                            }

                            return '<span class="' + badgeClass + '">' + roleText + '</span>';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var currentUserId = {{ auth()->user()->id }};
                            var row_id = row.id;
                            var deleteButton = '';

                            // Only show delete button if current user is not deleting themselves
                            if (currentUserId != row_id) {
                                deleteButton =
                                    '<a href="javascript:;" class="text-danger delete-user" data-id="' +
                                    row.id + '" title="Delete">' +
                                    '<i class="mdi mdi-delete-outline"></i>' +
                                    '</a>';
                            }

                            return '<div class="d-flex gap-2">' +
                                '<a href="javascript:;" class="text-primary edit-user" data-id="' +
                                row.id + '" title="Edit">' +
                                '<i class="mdi mdi-pencil-outline"></i>' +
                                '</a>' +
                                deleteButton +
                                '</div>';
                        }
                    }
                ],
                searching: false,
                lengthChange: false,
                responsive: true
            });

            // Form validation
            $('#createUserForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 50
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        minlength: 6 // remove 'required: true'
                    },
                    user_role: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: "Please enter user name",
                        minlength: "Name must be at least 2 characters long",
                        maxlength: "Name cannot exceed 50 characters"
                    },
                    email: {
                        required: "Please enter email address",
                        email: "Please enter a valid email address"
                    },
                    password: {
                        minlength: "Password must be at least 6 characters long"
                    },
                    user_role: {
                        required: "Please select a role"
                    }
                },
                errorElement: 'span',
                errorClass: 'invalid-feedback',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var formData = $(form).serialize();
                    var editId = $('#createUserForm').data('edit-id');
                    var url = editId ? '/users/' + editId : "{{ route('users.store') }}";
                    var type = editId ? 'PUT' : 'POST';

                    $.ajax({
                        url: url,
                        type: type,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#createUserModal').modal('hide');
                            form.reset();
                            $('#users-table').DataTable().ajax.reload();
                            notyf.success(response.message);
                            // Reset to create mode
                            $('#createUserForm').removeData('edit-id');
                            $('#createUserModalLabel').text('Create New User');
                            $('#createUserForm .btn-primary').text('Save');
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $('#' + key + '_error').text(value[0]);
                                    $('#' + key).addClass('is-invalid');
                                });
                            } else {
                                notyf.error('An error occurred. Please try again.');
                            }
                        }
                    });

                    return false;
                }
            });

            // When opening for create
            $('.btn-primary[data-bs-target="#createUserModal"]').on('click', function() {
                $('#createUserForm').removeData('edit-id');
                $('#createUserForm').validate().settings.rules.password.required = true;
                $('#password').attr('required', true);
                $('#password').val('');
            });

            // When opening for edit
            $(document).on('click', '.edit-user', function() {
                var userId = $(this).data('id');
                // Fetch category data from controller
                $.ajax({
                    url: '/users/' + userId +
                        '/edit', // You need to create this route/controller method
                    type: 'GET',
                    success: function(response) {
                        // Fill form fields
                        $('#name').val(response.name);
                        $('#email').val(response.email);
                        $('#user_role').val(response.user_role);
                        $('#user_role').trigger('change');
                        if (response.is_selling_tea) {
                            $('#is_selling_tea').prop('checked', true);
                        } else {
                            $('#is_selling_tea').prop('checked', false);
                        }

                        // Remove previous errors
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        // Change modal label and button
                        $('#createUserModalLabel').text('Edit Category');
                        $('#createUserForm .btn-primary').text('Update');
                        // Store id for update
                        $('#createUserForm').data('edit-id', userId);
                        // Open modal
                        $('#createUserModal').modal('show');
                        $('#createUserForm').validate().settings.rules.password.required =
                            false;
                        $('#password').removeAttr('required');
                        $('#password').val('');
                    },
                    error: function() {
                        notyf.error('Failed to fetch category data.');
                    }
                });
            });

            // Reset form when modal is closed
            $('#createUserModal').on('hidden.bs.modal', function() {
                $('#createUserForm')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#is_selling_tea_wrapper').hide();
                $('#createUserModalLabel').text('Create New User');
                $('#createUserForm .btn-primary').text('Save');
            });

            // Delete user button click
            $(document).on('click', '.delete-user', function() {
                var userId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('users.destroy') }}',
                            type: 'DELETE',
                            data: {
                                id: userId,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Reload DataTable
                                $('#users-table').DataTable().ajax.reload();

                                // Show success message
                                notyf.success(responce.message);
                            },
                            error: function(xhr) {
                                notyf.error(
                                    'An error occurred while deleting the user.'
                                );
                            }
                        });
                    }
                });
            });

            $('#user_role').on('change', function() {
                if ($(this).val() == '2') { // 2 = Vendor
                    $('#is_selling_tea_wrapper').show();
                } else {
                    $('#is_selling_tea_wrapper').hide();
                    $('#is_selling_tea').prop('checked', false);
                }
            });
        });
    </script>
@endsection
