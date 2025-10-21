<!-- ✅ Modern Footer -->
<footer class="bg-dark text-light py-3 mt-auto shadow-sm fixed-bottom">
  <div class="container text-center">
    <p class="mb-0">
      © <span id="year"></span> smart Store — All Rights Reserved
    </p>
    <small class="text-secondary">Crafted with ❤️ by Asma Aslam</small>
  </div>
</footer>

<script>
  // ✅ Auto-update year
  document.getElementById("year").textContent = new Date().getFullYear();
</script>
