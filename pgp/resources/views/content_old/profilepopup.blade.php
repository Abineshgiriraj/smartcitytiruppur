@extends('layouts/contentNavbarLayout')

@section('title', 'Tables - Basic Tables')
<link rel="stylesheet" href="{{ asset('public/assets/css/cropper.min.css') }}" />


<style>
    .btn-right {
        float: right;
    }

    #cropProfileButton {
        background-color: #007BFF;
        color: white;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
    }

    #cropProfileButton:hover {
        background-color: #0056b3;
    }


    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .preview-container {
        margin-top: 20px;
    }

    #videoElement {
        width: 100%;
        max-width: 480px;
        height: auto;
        border: 2px solid #ccc;
    }

    #imagePreview {
        width: 100%;
        max-width: 480px;
        height: auto;
        border: 2px dashed #ccc;
    }

    #croppedImage {

        display: block;
        width: 455%;
        height: 153px;
        margin-top: -576px;
        /* margin-left: 605px; */
        margin-left: 555px;
        object-fit: contain;
    }


    button {
        margin-top: 10px;
        margin-right: 10px;
        padding: 8px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:disabled {
        background-color: #aaa;
        cursor: not-allowed;
    }
    /* Make the overall container take the full height of the page */
.over-all-dv {
    height: 100vh;  /* Full viewport height */
    display: flex;
    flex-direction: column;
}

/* Make the form container scrollable */
.card-body {
    overflow-y: auto;  /* Add vertical scrollbar */
    max-height: calc(100vh - 100px);  /* Allow scrolling, adjust as needed */
    padding: 20px;
}

/* Optional: Set padding or margin for the scroll container */
.pop-lable {
    margin-bottom: 20px;
}
.position-relative {
        position: relative;
    }
    .password-input {
        padding-right: 35px; /* Space for the eye icon */
    }
    .toggle-password {
        position: absolute;
        right: 16px;
        top: 23%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
    }
    .toggle-password:hover {
        color: #333;
    }
</style>

@section('content')


<script src="{{ asset('public/assets/js/cropper.min.js') }}"></script>

<div class="over-all-dv">
    <div class="card">
        <div class="card-body">
            <div class="pop-lable">
                <form id="editUserForm" method="post" onsubmit="return submitForm(event);">
                    <div class="ds">
                        <div class="pop-hed mb-3">
                            <h4>Edit Profile </h4>
                        </div>

                        <div class="row ">
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">Staff Name:</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mb-2" id="staff_name" name="staff_name" value="{{ $users->staff_name }}" oninput="validatename(this)" />
                                <div id="staff_name_Error" class="text-danger"></div>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">Mobile No:</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="tel" class="form-control" id="mobile_number" name="mobile_number" maxlength="10" pattern="\d{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="{{ $users->mobile_number }}" />
                                <div id="mobile_number_Error" class="text-danger"></div>
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">Email Id:</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="email" class="form-control mb-2" id="email_id" name="email_id" value="{{ $users->email_id }}" oninput="mail_valid(this)" />
                                <div id="email_id_Error" class="text-danger"></div>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">Designation:</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mb-2" id="designation" name="designation" value="{{ $users->remarks }}" oninput="validatename(this)" />
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">username:</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mb-2" id="user_name" name="user_name" value="{{ $users->user_name }}" oninput="validatename(this)" />
                                <div id="user_name_Error" class="text-danger"></div>
                            </div>
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">UserType:</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mb-2" id="" name="" value="{{ $users->user_type }}" readonly />
                                <input type="hidden" class="form-control mb-2" id="user_type" name="user_type" value="{{ $users->user_type_id }}" readonly />
                            </div>
                        </div>

                        <div class="row ">
                            <div class="col-sm-2 align-self-center">
                                <label for="colFormLabelSm" class="col-form-label">Photo</label>
                                <span class="text-danger">*</span>
                            </div>
                            <div class="col-sm-4">
                                <select class="form-select" name="photo_pic" id="photo_pic" onchange="handleSelectChange()">
                                    <option value="">Select</option>
                                    <option value="upload_pic">Upload</option>
                                    <option value="snap_pic">Snap</option>
                                </select>
                            </div>
                        </div>

                        <!-- Modal for Uploading Photo -->
                        <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="uploadModalLabel">Upload Photo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div style="text-align: center;">
                                            <input type="file" id="croppedImageInput" name="croppedImageInput" accept=".jpeg,.jpg,.png" onchange="handleFileSelect()" />
                                            <div style="margin-top: 20px;">
                                                <img id="profilePhoto" style="max-width: 100%; display: none;" />
                                            </div>
                                            <button type="button" id="cropProfileButton" style="margin-top: 20px;" onclick="cropImage()">Crop Profile Photo</button>
                                            <input type="hidden" id="photo" name="photo" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="profilePhotoResult" style="margin-top: 20px;"></div>

                        <!-- Modal for Taking Snap -->
                        <div class="modal fade" id="snapModal" tabindex="-1" aria-labelledby="snapModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="snapModalLabel">Snap Photo</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <video id="videoElement" autoplay style="width: 100%;"></video>
                                                <br>
                                                <button type="button" id="startWebcam" class="btn btn-primary" onclick="startWebcam()">Start Webcam</button>
                                                <button type="button" id="captureImage" class="btn btn-secondary" onclick="captureImage()">Capture Image</button>
                                            </div>
                                            <div class="col-12 col-sm-6 preview-container">
                                                <canvas id="imagePreview" style="width: 100%; border: 1px solid #ccc;"></canvas>
                                                <br>
                                                <button type="button" id="cropProfileButtons" class="btn btn-success" style="margin-top: 20px;" onclick="cropSnap()">Crop Profile Photo</button>
                                                <input type="hidden" id="photos" name="photos" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Display Cropped Image Outside Modal -->
                        <div id="croppedImageResult" style="margin-top: 20px; text-align: center;"></div>

                        <div class="row">
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">Pos Machine Id:</label>
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control mb-2" id="pos_mac" name="pos_mac" value="{{ $users->posmachineid }}" />
                            </div>
                        </div>

                        <div class="row">
                            <label>
                                <input type="checkbox" id="exampleCheckbox" value="option1" onchange="toggleChangePassword()">
                                Change Password
                            </label>
                        </div>
                        <div class="row " id="change_password" style="display:none;">
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">New Password:</label>
                            </div>
                            <div class="col-sm-4 position-relative">
    <input type="password" class="form-control mb-2 password-input" id="password" name="password" />
    <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
</div>
                            <div class="col-sm-2">
                                <label class="col-form-label" style="display: inline-block; margin-right: 10px;">New Conform Password:</label>
                            </div>
                            <div class="col-sm-4 position-relative">
                                <input type="password" class="form-control mb-2" id="password_confirmation" name="password_confirmation" value="" />
                                <i class="fas fa-eye-slash toggle-password" id="togglePasswords"></i>
                            </div>
                        </div>

                        <div class="row">
                            <div class="btn-float">
                                <button type="button" class="btn btn-danger" onclick="window.location.href='{{ route('dashboardindex') }}';">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/dataTables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/responsive.bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('public/assets/css/core.css') }}" />

    <script src="{{ asset('public/assets/js/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('public/assets/js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('public/assets/js/responsive.bootstrap5.js') }}"></script>

    <script src="{{ asset('public/assets/js/sweet_alert.js') }}"></script>
    <script src="{{ asset('public/assets/js/common.js') }}"></script>



    <script>

        document.addEventListener("DOMContentLoaded", () => {
            const uploadProfilePhoto = document.getElementById('croppedImageInput');
            const profilePhoto = document.getElementById('profilePhoto');
            const cropProfileButton = document.getElementById('cropProfileButton');
            const cropProfileButtons = document.getElementById('cropProfileButtons');
            const croppedImageInput = document.getElementById('photo');
            const croppedImageInputs = document.getElementById('photos');
            const profilePhotoResult = document.getElementById('profilePhotoResult');
            const videoElement = document.getElementById('videoElement');
            const imagePreview = document.getElementById('imagePreview');
            const cropImage = document.getElementById('cropProfileButtons');

            let cropper;

            const fixedWidth = 200;  // Fixed output width
            const fixedHeight = 200; // Fixed output height

            // Handle file upload
            uploadProfilePhoto.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        profilePhoto.src = e.target.result;
                        profilePhoto.style.display = 'block';
                        cropProfileButton.style.display = 'inline-block';

                        // Initialize Cropper.js for the uploaded image
                        if (cropper) cropper.destroy(); // Destroy existing cropper instance
                        cropper = new Cropper(profilePhoto, {
                            aspectRatio: 1, // Fixed square aspect ratio
                            viewMode: 1,
                            minContainerWidth: 400,
                            minContainerHeight: 400,
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Start webcam when clicked
            document.getElementById('startWebcam').addEventListener('click', async () => {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    videoElement.srcObject = stream;
                    document.getElementById('snap').style.display = 'block';
                } catch (error) {
                    alert('Unable to access webcam: ' + error.message);
                }
            });

            // Capture image when clicked
            document.getElementById('captureImage').addEventListener('click', () => {
                const canvas = imagePreview.getContext('2d');
                imagePreview.width = videoElement.videoWidth;
                imagePreview.height = videoElement.videoHeight;
                canvas.drawImage(videoElement, 0, 0, videoElement.videoWidth, videoElement.videoHeight);

                // Initialize cropper after image capture (in snap mode)
                if (cropper) cropper.destroy();  // Destroy previous cropper if any
                cropper = new Cropper(imagePreview, {
                    viewMode: 1,
                    aspectRatio: NaN, // Free aspect ratio for the snap photo
                });
            });



            cropProfileButtons.addEventListener('click', () => {
                if (cropper) {
                    // Get the cropped canvas from the cropper
                    const croppedCanvas = cropper.getCroppedCanvas({
                        width: fixedWidth,
                        height: fixedHeight,
                    });

                    // Convert the canvas to a base64 image
                    const croppedImage = croppedCanvas.toDataURL('image/png');
                    croppedImageInputs.value = `cropped-image-${Date.now()}-${Math.floor(Math.random() * 10000)}.png`;
                    // Convert the base64 image to a Blob
                    fetch(croppedImage)
                        .then(res => res.blob())  // Convert base64 to Blob
                        .then(blob => {
                            // Create a new File from the Blob
                            const file = new File([blob], `crp-${Date.now()}.png`, { type: 'image/png' });

                            // Create a DataTransfer object to simulate file input
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);

                            // Set the file input's files to the new file
                            uploadProfilePhoto.files = dataTransfer.files;

                            // Optionally display the cropped image in the result section
                            profilePhotoResult.innerHTML = `<img src="${croppedImage}" style="border-radius: 50%; max-width: 100%;">`;
                        })
                        .catch(error => {
                            console.error("Error converting base64 to file:", error);
                        });
                }
            });

            cropProfileButton.addEventListener('click', () => {
                if (cropper) {
                    // If cropper is initialized for uploaded photo
                    const canvas = cropper.getCroppedCanvas({
                        width: fixedWidth,
                        height: fixedHeight,
                    });

                    const croppedImage = canvas.toDataURL('image/png'); // Get cropped image as Base64
                    croppedImageInput.value = `crp-${Date.now()}-${Math.floor(Math.random() * 10000)}.png`;
                    fetch(croppedImage)
                        .then(res => res.blob())  // Convert base64 to Blob
                        .then(blob => {
                            // Create a new File from the Blob
                            const file = new File([blob], `crp-${Date.now()}.png`, { type: 'image/png' });

                            // Create a DataTransfer object to simulate file input
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);

                            // Set the file input's files to the new file
                            uploadProfilePhoto.files = dataTransfer.files;

                            // Optionally display the cropped image
                            profilePhotoResult.innerHTML = `<img src="${croppedImage}" style="border-radius: 50%; max-width: 100%;">`;
                        })
                }
            });

            // Handle dropdown change for showing the appropriate input
            window.image_upload = () => {
                const choosePic = document.getElementById('photo_pic').value;
                if (choosePic === 'upload_pic') {
                    document.getElementById('upload').style.display = 'block';
                    document.getElementById('snap').style.display = 'none';
                } else if (choosePic === 'snap_pic') {
                    document.getElementById('snap').style.display = 'block';
                    document.getElementById('upload').style.display = 'none';
                } else {
                    document.getElementById('upload').style.display = 'none';
                    document.getElementById('snap').style.display = 'none';
                }
            };
        });




        function submitForm(event) {
            event.preventDefault();

            const fileInput = document.getElementById('croppedImageInput');
            const file = fileInput.files[0]; // Get the selected file
            const photo = $('#photo').val(); // Get the photo value (e.g., base64 or file name)
            const photos = $('#photos').val(); // Get the photo value (e.g., base64 or file name)
            var password = $('#password').val();
            var passwordConfirmation = $('#password_confirmation').val();

            // Check if password and confirmation match
            if (password !== passwordConfirmation) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'The passwords you entered do not match. Please try again.',
                });
                return; // Prevent form submission if passwords don't match
            }
            let formData = new FormData();
            formData.append('staff_name', $('#staff_name').val());
            formData.append('user_name', $('#user_name').val());
            formData.append('password', $('#password').val());
            formData.append('password_confirmation', $('#password_confirmation').val());
            formData.append('user_type', $('#user_type').val());
            formData.append('pos_mac', $('#pos_mac').val());
            formData.append('mobile_number', $('#mobile_number').val());
            formData.append('designation', $('#designation').val());
            formData.append('email_id', $('#email_id').val());
            formData.append('photo_pic', $('#photo_pic').val());

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');

            // Append the actual file (if available) to FormData
            if (file) {
                formData.append('photo', file);
            } else {
                formData.append('photo', photos);
            }

            let url = '{{ route("ProfileWindowupdate", $users->unique_id) }}';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false, // Important to prevent jQuery from processing data
                contentType: false, // Important to send multipart form data
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'User updated successfully!',
                    }).then(() => {
                        window.location.href = '{{ route("loginindex") }}';
                    });
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    $('.text-danger').html(''); // Clear any previous errors
                    $.each(errors, function (key, value) {
                        $('#' + key + '_Error').html(value[0]);
                    });
                }
            });
        }

        function toggleChangePassword() {
            // Get the checkbox and the change_password div
            const checkbox = document.getElementById('exampleCheckbox');
            const changePasswordDiv = document.getElementById('change_password');

            // Show or hide the div based on the checkbox state
            if (checkbox.checked) {
                changePasswordDiv.style.display = 'flex'; // Use flex for proper alignment
            } else {
                changePasswordDiv.style.display = 'none';
            }
        }
    </script>
    <!-- Add Bootstrap 5 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="{{ asset('public/assets/js/bootstrap.bundle.min.js') }}"></script>


    <script>
        // Function to handle select change and show the appropriate modal
        // Function to handle select change and show the appropriate modal
        function handleSelectChange() {
            const selectedOption = document.getElementById("photo_pic").value;

            if (selectedOption === "upload_pic") {
                // Show upload modal
                const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
                document.getElementById('croppedImageInput').value = ''; // Reset the file input
                document.getElementById('profilePhoto').style.display = 'none'; // Hide the previous image preview
                document.getElementById('profilePhotoResult').innerHTML = ''; // Clear any cropped image result
                document.getElementById('photo').value = '';
                uploadModal.show();
            } else if (selectedOption === "snap_pic") {
                // Show snap modal
                const snapModal = new bootstrap.Modal(document.getElementById('snapModal'));
                snapModal.show();
                // Reset the canvas
                const canvas = document.getElementById('imagePreview');
                const context = canvas.getContext('2d');

                // Clear the canvas (this will erase any previous image drawn)
                context.clearRect(0, 0, canvas.width, canvas.height);
                document.getElementById('profilePhotoResult').innerHTML = ''; // Clear cropped photo preview
                document.getElementById('photos').value = '';
                // Once the modal is fully shown, you can safely initialize the webcam
                document.getElementById('snapModal').addEventListener('shown.bs.modal', function () {
                    startWebcam(); // Start webcam when the modal is shown
                });
            }
        }

        // Start webcam for snapping image
        function startWebcam() {
            const videoElement = document.getElementById("videoElement");
            const captureImageButton = document.getElementById("captureImage");

            // Request webcam access
            navigator.mediaDevices.getUserMedia({ video: true })
                .then((stream) => {
                    videoElement.srcObject = stream;

                    // Once the webcam is ready, show the "Capture Image" button
                    captureImageButton.style.display = "inline-block"; // Show button
                })
                .catch((error) => {
                    alert('Unable to access webcam: ' + error.message);
                });
        }

        // Function to capture the image from webcam
        function captureImage() {
            const videoElement = document.getElementById('videoElement');
            const canvas = document.getElementById('imagePreview');
            const ctx = canvas.getContext('2d');

            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;

            ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
            document.getElementById("cropProfileButtons").style.display = "inline-block";
        }




        // Handle file input change for uploading an image
        function handleFileSelect() {
            const fileInput = document.getElementById('croppedImageInput');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const profilePhoto = document.getElementById('profilePhoto');
                    profilePhoto.src = e.target.result;
                    profilePhoto.style.display = 'block';
                    document.getElementById('cropProfileButton').style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Crop the uploaded image using Cropper.js
        function cropImage() {
            $("#uploadModal").hide();
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove(); // Remove the backdrop from the DOM
            }
            const profilePhoto = document.getElementById('profilePhoto');
            const cropper = new Cropper(profilePhoto, {
                aspectRatio: 1, // square crop
                viewMode: 1,
                minContainerWidth: 400,
                minContainerHeight: 400,
            });

            const croppedCanvas = cropper.getCroppedCanvas({
                width: 200,
                height: 200
            });

            const croppedImage = croppedCanvas.toDataURL('image/png');
            document.getElementById('profilePhotoResult').innerHTML = `<img src="${croppedImage}" style="border-radius: 50%; max-width: 100%;">`;

            // Display cropped image outside modal
            const croppedImageResult = document.getElementById('croppedImageResult');
            croppedImageResult.innerHTML = `<img src="${croppedImage}" style="border-radius: 50%; max-width: 100px; margin-top: 20px;">`;



        }


        async function cropSnap() {
            // Access the canvas and video elements
            $("#snapModal").hide();


            // $("#uploadModal").modal('hide');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove(); // Remove the backdrop from the DOM
            }
            const canvas = document.getElementById('imagePreview');
            const videoElement = document.getElementById('videoElement'); // Make sure you have the correct video element

            // Start the webcam stream
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            videoElement.srcObject = stream;
            document.getElementById('snap').style.display = 'block';

            // Initialize Bootstrap modals
            const snapModal = new bootstrap.Modal(document.getElementById('snapModal'));
            const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));

            // Initialize Cropper.js
            const cropper = new Cropper(canvas, {
                aspectRatio: 1, // square crop
                viewMode: 1,
                minContainerWidth: 400,
                minContainerHeight: 400,
            });

            // Get the cropped canvas
            const croppedCanvas = cropper.getCroppedCanvas({
                width: 200,
                height: 200
            });

            // Convert the cropped canvas to a data URL (image)
            const croppedImage = croppedCanvas.toDataURL('image/png');

            // Display cropped image inside the modal (profilePhotoResult container)
            document.getElementById('profilePhotoResult').innerHTML = `<img src="${croppedImage}" style="border-radius: 50%; max-width: 100%;">`;

            // Close the snap modal


            // Stop the webcam stream
            stream.getTracks().forEach(track => track.stop());

            // Display the cropped image outside the modal (croppedImageResult container)
            const croppedImageResult = document.getElementById('croppedImageResult');
            croppedImageResult.innerHTML = `<img src="${croppedImage}" style="border-radius: 50%; max-width: 100px; margin-top: 20px;">`;

            // Close the upload modal (if applicable)
            if (uploadModal) {
                uploadModal.hide();
            }
        }

        function mail_valid(input) {
            const mailError = document.getElementById('email_id_Error');

            // Allowed characters for the email
            const allowedChars = [
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
                'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
                'u', 'v', 'w', 'x', 'y', 'z',
                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
                'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
                'U', 'V', 'W', 'X', 'Y', 'Z',
                ' ', '_', '.', '@'
            ];

            // Filter out characters that are not in the allowedChars array
            input.value = input.value.split('').filter(char => allowedChars.includes(char)).join('');

            // Regular expression for validating an email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Get the trimmed value of the input
            const emailValue = input.value.trim();

            // Test the input against the email pattern
            if (emailValue.length > 0 && !emailPattern.test(emailValue)) {
                mailError.textContent = 'Enter a valid email ID.';
            } else {
                mailError.textContent = ''; // Clear the error message if valid
            }
        }

        function validateAddress(element) {
            const addressRegex = /^[a-zA-Z0-9\s\/\-\.\(\)]*$/;
            const errorSpan = document.getElementById('address_error');
            const inputValue = element.value;

            if (!addressRegex.test(inputValue)) {
                // Remove invalid characters
                element.value = inputValue.replace(/[^a-zA-Z0-9\s\/\-\.\(\)]/g, '');
                errorSpan.textContent = "";
            } else {
                errorSpan.textContent = ""; // Clear error message if input is valid
            }
        }
        function validatename(element) {
    // Allow only alphabetic characters (a-z, A-Z) and spaces
    const nameRegex = /^[a-zA-Z\s]*$/;
    const errorSpan = document.getElementById('address_error');
    const inputValue = element.value;

    if (!nameRegex.test(inputValue)) {
        // Remove invalid characters
        element.value = inputValue.replace(/[^a-zA-Z\s]/g, '');
        errorSpan.textContent = "Only letters and spaces are allowed"; // Set error message
    } else {
        errorSpan.textContent = ""; // Clear error message if input is valid
    }
}


const passwordField = document.getElementById('password');
const password_confirmation = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswords = document.getElementById('togglePasswords');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute for Password field
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle the eye icon for Password field
        this.classList.toggle('fa-eye-slash');
        this.classList.toggle('fa-eye');
    });
    togglePasswords.addEventListener('click', function() {
        // Toggle the type attribute for Password field
        const type = password_confirmation.getAttribute('type') === 'password' ? 'text' : 'password';
        password_confirmation.setAttribute('type', type);

        // Toggle the eye icon for Password field
        this.classList.toggle('fa-eye-slash');
        this.classList.toggle('fa-eye');
    });
    </script>
    @endsection
