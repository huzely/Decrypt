    </main>
</div>
<script>
(function(){
  const btn = document.getElementById('openNav');
  const sidebar = document.getElementById('sidebarNav');
  if (btn && sidebar) {
    btn.addEventListener('click', function(){
      sidebar.classList.toggle('open');
    });
    sidebar.addEventListener('click', function(e){
      if (e.target.tagName === 'A') { sidebar.classList.remove('open'); }
    });
  }
})();
</script>
</body>
</html>
