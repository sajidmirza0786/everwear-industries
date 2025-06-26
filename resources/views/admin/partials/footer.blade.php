<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
   <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
      <div class="mb-2 mb-md-0">
         © 2025
         , made with ❤️ by
         <a href="https://themeselection.com" target="_blank" class="footer-link fw-bolder">Cypwebtech</a>
      </div>
   </div>
</footer>
<!-- / Footer -->
<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
<!-- / Layout page -->
</div>
<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->
{{-- <div class="buy-now">
   <a
      href="https://themeselection.com/products/sneat-bootstrap-html-admin-template/"
      target="_blank"
      class="btn btn-danger btn-buy-now"
      >Upgrade to Pro</a
      >
</div> --}}


{{-- Global Loader Overlay --}}
<div id="globalLoaderOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
{{-- End Global Loader Overlay --}}

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ url('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ url('admin/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ url('admin/assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ url('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ url('admin/assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->
<!-- Vendors JS -->
<script src="{{ url('admin/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<!-- Main JS -->
<script src="{{ url('admin/assets/js/main.js') }}"></script>
<!-- Page JS -->
<script src="{{ url('admin/assets/js/dashboards-analytics.js') }}"></script>
<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.2/dist/sweetalert2.all.min.js"></script>


<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<style type="text/css">
  /* Custom SweetAlert2 Toast z-index */
.swal2-container {
    z-index: 9999 !important;
}
</style>
{{-- Global Form Submission Loader Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const globalLoader = document.getElementById('globalLoaderOverlay');

            // Listen for all form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    // Check if the form is valid before showing the loader
                    if (this.checkValidity()) {
                        if (globalLoader) {
                            globalLoader.style.display = 'flex';
                        }

                        // Optional: Disable all submit buttons within the submitted form
                        this.querySelectorAll('button[type="submit"]').forEach(button => {
                            button.disabled = true;
                            // Add a spinner to the button itself
                            if (!button.querySelector('.spinner-border')) {
                                button.insertAdjacentHTML('afterbegin', '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>');
                            }
                            // Store original text to restore later (if needed, though page reloads will handle it)
                            button.setAttribute('data-original-text', button.textContent.trim());
                            button.textContent = 'Processing...';
                        });
                    }
                    // If form.checkValidity() returns false, the browser will block the submission
                    // and show validation messages, so the loader won't appear prematurely.
                });
            });

            // Hide loader and re-enable buttons on page load (e.g., after server-side validation errors)
            // This ensures the loader isn't stuck if the page reloads due to backend errors.
            window.addEventListener('load', function() {
                if (globalLoader) {
                    globalLoader.style.display = 'none';
                }
                document.querySelectorAll('form').forEach(form => {
                    form.querySelectorAll('button[type="submit"]').forEach(button => {
                        button.disabled = false;
                        // Remove any added spinner and restore original text
                        const existingSpinner = button.querySelector('.spinner-border');
                        if (existingSpinner) {
                            existingSpinner.remove();
                        }
                        const originalText = button.getAttribute('data-original-text');
                        if (originalText) {
                            button.textContent = originalText;
                            button.removeAttribute('data-original-text'); // Clean up attribute
                        }
                    });
                });
            });
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',  // Can be 'top-right', 'top-left', 'top-center', etc.
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    container: 'swal2-container',
                },
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',  // Can be 'top-right', 'top-left', 'top-center', etc.
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    container: 'swal2-container',
                },
            });
        @endif
    });
</script>

@yield('scripts')

</body>
</html>