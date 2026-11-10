// ===========================================================
// WAIT FOR HTML TO LOAD BEFORE RUNNING SCRIPT (Best Practice)
// ===========================================================
document.addEventListener('DOMContentLoaded', () => {

   // ==========================================================
   // 1. BACK TO TOP BUTTON LOGIC
   // ==========================================================
   const toTopBtn = document.getElementById('to-top-button');

   if (toTopBtn) {
      window.addEventListener('scroll', () => {
         if (window.scrollY > 300) {
            toTopBtn.classList.add('show');
         } else {
            toTopBtn.classList.remove('show');
         }
      });

      toTopBtn.addEventListener('click', () => {
         window.scrollTo({
            top: 0,
            behavior: 'smooth'
         });
      });
   }


   // ==========================================================
   // 2. FORM RENDER TIMESTAMP (SPAM PREVENTION)
   // ==========================================================
   const renderTimeInput = document.getElementById('form_render_time');
   if (renderTimeInput) {
      renderTimeInput.value = Math.floor(Date.now() / 1000);
   }


   // ==========================================================
   // 3. CONTACT FORM STATUS NOTIFICATION HANDLING
   // ==========================================================
   const urlParams = new URLSearchParams(window.location.search);
   const status = urlParams.get('status');
   const responseDiv = document.getElementById('form-response');

   if (responseDiv && status) {
      if (status === 'success') {
         responseDiv.innerHTML = '<strong>Thank you!</strong> Your message has been sent successfully. We will get back to you shortly.';
         responseDiv.className = 'form-response success';
         responseDiv.style.display = 'block';
      } else if (status === 'error') {
         responseDiv.innerHTML = '<strong>Something went wrong.</strong> Please try sending your message again or contact us directly.';
         responseDiv.className = 'form-response error';
         responseDiv.style.display = 'block';
      }

      // Clean up URL parameters without reloading the page
      window.history.replaceState({}, document.title, window.location.pathname + '#contact');
   }

});
