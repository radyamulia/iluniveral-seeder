<div class="footer bg-primer">
  <div class="copyright text-center">
      <p>Copyright &copy; Designed & Developed by <a href="https://themeforest.net/user/quixlab">Univeral</a> 2024</p>
  </div>
</div>

<!--********************************** Scripts JS ***********************************-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function() {
      // Menangani klik tombol untuk membuka modal
      $('#openBtn').click(function() {
      $('#myModal').modal('show'); // Menampilkan modal
      });
  });

  $('#closeModalBtn').click(function() {
      $('#myModal').modal('hide'); // Menyembunyikan modal
  });

</script>