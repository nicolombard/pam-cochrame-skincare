
const backToTopBtn = document.getElementById('backToTopBtn');

// Only run the code if the button actually exists on the page
if (backToTopBtn) {
   // Show or hide the button based on scroll position
   window.addEventListener('scroll', () => {
      if (window.scrollY > 300) {
         backToTopBtn.classList.add('show');
      } else {
         backToTopBtn.classList.remove('show');
      }
   });

   // Smoothly scroll back to top on click
   backToTopBtn.addEventListener('click', () => {
      window.scrollTo({
         top: 0,
         behavior: 'smooth'
      });
   });
}