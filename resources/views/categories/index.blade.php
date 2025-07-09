@extends('layouts/contentNavbarLayout')

@section('title', 'Categories')

@section('content')
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Categories /</span> Categories List
    </h4>

    <!-- Hoverable Table rows -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Categories List</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus"></i> Create Category
            </button>
        </div>
        <div class="card-body tablecard">
            <table id="main-table" class="table table-striped table-hover align-middle rounded-3" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--/ Hoverable Table rows -->

    <!-- Create  Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter category name">
                            <div class="invalid-feedback" id="name_error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image *</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*"
                                onchange="previewImage(event)">
                            <div class="invalid-feedback" id="image_error"></div>
                            <div class="mt-2">
                                <img id="image_preview" src="" alt="Image Preview"
                                    style="width: 100%; border-radius:2rem; display: none; object-fit: cover; max-height: 200px;" />
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
            $('#main-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('categories.list') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data) {
                                var imageUrl = '/images/categories/' + data;
                                return '<img src="' + imageUrl +
                                    '" alt="Image" style="max-width:60px; max-height:60px; border-radius:0.5rem; object-fit:cover;" />';
                            } else {
                                return '<span class="text-muted">No Image</span>';
                            }
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
                                    '<a href="javascript:;" class="text-danger delete-data" data-id="' +
                                    row.id + '" title="Delete">' +
                                    '<i class="mdi mdi-delete-outline"></i>' +
                                    '</a>';
                            }

                            return '<div class="d-flex gap-2">' +
                                '<a href="javascript:;" class="text-primary edit-data" data-id="' +
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
            $('#createForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2,
                        maxlength: 50
                    },
                    image: {
                        extension: "jpg|jpeg|png|gif|bmp|webp",
                    },
                },
                messages: {
                    name: {
                        required: "Please enter category name",
                        minlength: "Name must be at least 2 characters long",
                        maxlength: "Name cannot exceed 50 characters"
                    },
                    image: {
                        extension: "Only image files are allowed (jpg, jpeg, png, gif, bmp, webp)"
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
                    var formData = new FormData(form);
                    var editId = $('#createForm').data('edit-id');
                    var url = editId ? '/categories/' + editId : "{{ route('categories.store') }}";
                    var type = 'POST';

                    $.ajax({
                        url: url,
                        type: type,
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Close modal
                            $('#createModal').modal('hide');

                            // Reset form
                            form.reset();

                            // Reload DataTable
                            $('#main-table').DataTable().ajax.reload();

                            // Show success message
                            notyf.success(responce.message);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                // Validation errors from server
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

                    return false; // Prevent default form submission
                }
            });

            // When opening for create
            $('.btn-primary[data-bs-target="#createModal"]').on('click', function() {
                $('#createForm').removeData('edit-id');
                $('#createForm').validate().settings.rules.image.required = true;
                $('#image').attr('required', true);
                $('#image').val('');
            });

            // Reset form when modal is closed
            $('#createModal').on('hidden.bs.modal', function() {
                $('#createForm')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#createModalLabel').text('Create Category');
                $('#createForm .btn-primary').text('Save');
                clearImagePreview();
            });


            // Edit button click
            $(document).on('click', '.edit-data', function() {
                var userId = $(this).data('id');
                // Fetch category data from controller
                $.ajax({
                    url: '/categories/' + userId +
                        '/edit', // You need to create this route/controller method
                    type: 'GET',
                    success: function(response) {
                        // Fill form fields
                        $('#name').val(response.name);
                        // Set image preview
                        if (response.image) {
                            var imageUrl = '/images/categories/' + response.image;
                            $('#image_preview').attr('src', imageUrl).show();
                        } else {
                            $('#image_preview').attr('src', '').hide();
                        }
                        // Remove previous errors
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        // Change modal label and button
                        $('#createModalLabel').text('Edit Category');
                        $('#createForm .btn-primary').text('Update');
                        // Store id for update
                        $('#createForm').data('edit-id', userId);
                        // Open modal
                        $('#createModal').modal('show');
                        $('#createForm').validate().settings.rules.image.required =
                            false;
                        $('#image').removeAttr('required');
                        $('#image').val('');
                    },
                    error: function() {
                        notyf.error('Failed to fetch category data.');
                    }
                });
            });

            // Delete  button click
            $(document).on('click', '.delete-data', function() {
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
                            url: '{{ route('categories.destroy') }}',
                            type: 'DELETE',
                            data: {
                                id: userId,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Reload DataTable
                                $('#main-table').DataTable().ajax.reload();

                                // Show success message
                                notyf.success(responce.message);
                            },
                            error: function(xhr) {
                                notyf.error(
                                    'An error occurred while deleting the data.'
                                );
                            }
                        });
                    }
                });
            });
        });

        function previewImage(event) {
            var input = event.target;
            var preview = document.getElementById('image_preview');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }

        // Reset form when modal is closed
        function clearImagePreview() {
            var preview = document.getElementById('image_preview');
            if (preview) {
                preview.src = '#';
                preview.style.display = 'none';
            }
            var imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.value = '';
            }
        }
    </script>
@endsection
