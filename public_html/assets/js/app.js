(function(){
  const hamburger=document.getElementById('hamburger');
  const menu=document.getElementById('mobileMenu');
  if(hamburger&&menu){hamburger.addEventListener('click',()=>{menu.classList.toggle('open');});}
})();
