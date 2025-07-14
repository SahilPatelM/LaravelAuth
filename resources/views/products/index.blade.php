@extends('layouts/contentNavbarLayout')

@section('title', 'Products')

@section('content')
    <h4 class="py-3 mb-4"><span class="text-muted fw-light"> Products /</span> Products List
    </h4>

    <!-- Hoverable Table rows -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Products List</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus"></i> Create Product
            </button>
        </div>
        <div class="card-body tablecard">
            <table id="main-table" class="table table-striped table-hover align-middle rounded-3" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
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
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Enter product description"></textarea>
                            <div class="invalid-feedback" id="description_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price *</label>
                            <input type="number" class="form-control" id="price" name="price"
                                placeholder="Product price">
                            <div class="invalid-feedback" id="price_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category *</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach ($category as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="category_id_error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="thumbnail_image" class="form-label">Thumbnail Image *</label>
                            <input type="file" class="form-control" id="thumbnail_image" name="thumbnail_image"
                                accept="image/*" onchange="previewImage(event)">
                            <div class="invalid-feedback" id="image_error"></div>
                            <div class="mt-2">
                                <img id="thumbnail_image_preview" src="" alt="Image Preview"
                                    style="width: 100%; border-radius:2rem; display: none; object-fit: cover; max-height: 200px;" />
                                <div id="existing_thumbnail_images_preview" class="d-flex flex-wrap gap-3">

                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="product_images" class="form-label">Product Images *</label>
                                <input type="file" class="form-control" id="product_images" name="images[]"
                                    accept="image/*" multiple onchange="previewMultipleImages(event)">
                                <div class="invalid-feedback" id="images_error"></div>

                                <div class="mt-3 d-flex flex-wrap gap-3" id="image_preview_container"></div>
                                <div id="existing_images_preview" class="d-flex flex-wrap gap-3">
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
            $('#category_id').select2({
                placeholder: "Select Category",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#createModal')
            });
            // Initialize DataTable
            $('#main-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('products.list') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category',
                    },
                    {
                        data: 'price',
                        name: 'price',
                    },
                    {
                        data: 'thumbnail',
                        name: 'thumbnail',
                        orderable: false,
                        searchable: false
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
                    var url = editId ? '/products/' + editId : "{{ route('products.store') }}";
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
                            notyf.success(response.message);
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
                $('#product_images').val('');
                $('#category_id').val('').trigger('change');
                $('#image_preview_container').empty();
                $('#existing_thumbnail_images_preview').empty();
                $('#existing_images_preview').empty();
                selectedFiles = [];
            });


            // Edit button click
            $(document).on('click', '.edit-data', function() {
                var userId = $(this).data('id');
                // Fetch category data from controller
                $.ajax({
                    url: '/products/' + userId +
                        '/edit', // You need to create this route/controller method
                    type: 'GET',
                    success: function(response) {
                        // Fill form fields
                        $('#name').val(response.name);
                        $('#description').val(response.description);
                        $('#price').val(response.price);
                        $('#category_id').val(response.category_id).trigger('change');
                        const container = $('#existing_images_preview');
                        const thumbcontainer = $('#existing_thumbnail_images_preview');
                        container.empty();
                        thumbcontainer.empty();

                        response.images.forEach(image => {
                            if (image.tag == 'thumbnail') {
                                const thumbBox = $(`
                                    <div class="position-relative" style="max-width: 120px;">
                                        <img src="/images/products/${image.image_path}" 
                                            class="img-thumbnail" 
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.5rem;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                                style="transform: translate(50%, -50%); padding: 0.25rem 0.5rem;'"
                                                onclick="removeImageProduct(${image.id}, this)">
                                            ×
                                        </button>
                                    </div>
                                `);
                                thumbcontainer.append(thumbBox);
                            } else {
                                const imgBox = $(`
                                                <div class="position-relative" style="max-width: 120px;">
                                                    <img src="/images/products/${image.image_path}" 
                                                        class="img-thumbnail" 
                                                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.5rem;">
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                                            style="transform: translate(50%, -50%); padding: 0.25rem 0.5rem;'"
                                                            onclick="removeImageProduct(${image.id}, this)">
                                                        ×
                                                    </button>
                                                </div>
                                            `);
                                container.append(imgBox);
                            }

                        });



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
                            url: '{{ route('products.destroy') }}',
                            type: 'DELETE',
                            data: {
                                id: userId,
                                _token: $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            success: function(response) {
                                // Reload DataTable
                                $('#main-table').DataTable().ajax.reload();

                                // Show success message
                                notyf.success(response.message);
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
            var preview = document.getElementById('thumbnail_image_preview');
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
            var preview = document.getElementById('thumbnail_image_preview');
            if (preview) {
                preview.src = '#';
                preview.style.display = 'none';
            }
            var imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.value = '';
            }
        }

        let selectedFiles = [];

        function previewMultipleImages(event) {
            const input = event.target;
            const container = document.getElementById('image_preview_container');
            container.innerHTML = '';
            selectedFiles = Array.from(input.files);

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewBox = document.createElement('div');
                    previewBox.classList.add('position-relative');
                    previewBox.style = 'max-width: 120px;';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview';
                    img.style =
                        'width: 100px; height: 100px; object-fit: cover; border-radius: 0.5rem;';

                    // const removeBtn = document.createElement('button');
                    // removeBtn.type = 'button';
                    // removeBtn.innerText = '×';
                    // removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
                    // removeBtn.style = 'transform: translate(50%, -50%); padding: 0.25rem 0.5rem;';
                    // removeBtn.onclick = function() {
                    //     removeImage(index);
                    // };

                    previewBox.appendChild(img);
                    // previewBox.appendChild(removeBtn);
                    container.appendChild(previewBox);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeImage(index) {
            selectedFiles.splice(index, 1); // Remove from the array

            // Create a new DataTransfer to update input.files
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            document.getElementById('product_images').files = dataTransfer.files;

            // Re-render preview
            previewMultipleImages({
                target: document.getElementById('product_images')
            });
        }

        function removeImageProduct(imageId, button) {
            $.ajax({
                url: `/products/product-images/${imageId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    $(button).closest('.position-relative').remove();
                    notyf.success("Image deleted successfully.");
                },
                error: function() {
                    notyf.error("Failed to delete image.");
                }
            });
        }
    </script>
@endsection
