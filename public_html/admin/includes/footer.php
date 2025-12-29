    </main>
</div>
<script>
(function(){
  const btn = document.getElementById('openNav');
  const sidebar = document.querySelector('.sidebar');
  if (btn && sidebar) {
    btn.addEventListener('click', function(){
      sidebar.classList.toggle('open');
    });
  }
})();
</script>
</body>
</html>
