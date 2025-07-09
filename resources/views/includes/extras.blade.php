<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="/logout">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript-->
<script src="{{ asset('vendor/jquery/jquery.min.js')}}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('js/sb-admin-2.min.js')}}"></script>

<!-- Page level plugins -->
<script src="{{ asset('vendor/chart.js/Chart.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{ asset('js/demo/chart-area-demo.js')}}"></script>
<script src="{{ asset('js/demo/chart-pie-demo.js')}}"></script>

<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>


<script>
        // Prevent Dropzone from auto-discovering and attaching to everything
        Dropzone.autoDiscover = false;

        let myDropzone = new Dropzone("div#image-upload", {
            url: "{{ route('image.upload') }}", // Set the URL for temporary uploads
            paramName: "file",
            maxFilesize: 2, // MB
            maxFiles: 1,
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            autoProcessQueue: true, // Automatically upload files when dropped
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            init: function() {
                // Called when a file is successfully uploaded
                this.on("success", function(file, response) {
                    // Set the hidden input's value to the temporary path
                    document.getElementById('image_path').value = response.path;
                });

                // Called when a file is removed
                this.on("removedfile", function(file) {
                    // Clear the hidden input's value
                    document.getElementById('image_path').value = '';
                    // Here you might also want to send an AJAX request to delete the temporary file from the server
                });

                // Called when maxFiles is reached
                this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            }
        });

        // Handle the final form submission
        document.getElementById('submit-all').addEventListener('click', function() {
            document.getElementById('student-form').submit();
        });
    </script>
