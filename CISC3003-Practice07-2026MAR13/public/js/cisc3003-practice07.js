document.addEventListener('DOMContentLoaded', function() {
   
    const inputs = document.querySelectorAll('input, textarea, select');
    
    inputs.forEach(field => {
     
        field.addEventListener('focus', function() {
            this.style.backgroundColor = "#FFE0B2";
        });
        
       
        field.addEventListener('blur', function() {
            this.style.backgroundColor = "";
           
            this.classList.remove('highlight');
        });
    });

   
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        let hasError = false;
        const requiredFields = document.querySelectorAll('.required');

        requiredFields.forEach(field => field.classList.remove('error'));

        requiredFields.forEach(field => {
            if (field.value.trim() === '') {
                field.classList.add('error');
                hasError = true;
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Please fill in all required fields!');
        }
    });

    
    const resetBtn = document.querySelector('button[type="reset"]');
    resetBtn.addEventListener('click', function() {
        document.querySelectorAll('.required').forEach(field => {
            field.classList.remove('error');
        });
        document.querySelectorAll('input, textarea').forEach(field => {
            field.style.backgroundColor = "";
        });
        form.reset();
    });
});