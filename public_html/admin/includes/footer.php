    </main>
</div>
<script>
(function(){
  const btn = document.getElementById('openNav');
  const sidebar = document.getElementById('sidebarNav');
  let backdrop = document.getElementById('drawerBackdrop');
  if (!backdrop) {
    backdrop = document.createElement('div');
    backdrop.id = 'drawerBackdrop';
    backdrop.className = 'drawer-backdrop';
    document.body.appendChild(backdrop);
  }
  function closeNav(){ sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
  if (btn && sidebar) {
    btn.addEventListener('click', function(){
      sidebar.classList.add('open');
      backdrop.classList.add('show');
    });
    backdrop.addEventListener('click', closeNav);
    sidebar.addEventListener('click', function(e){
      if (e.target.tagName === 'A') { closeNav(); }
    });
  }
})();
</script>
</body>
</html>
