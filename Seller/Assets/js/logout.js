document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('logout-link').addEventListener('click', function(event) {
        event.preventDefault();

        const animationElement = document.getElementById('logout-animation');
        
        animationElement.classList.add('show');
        
        localStorage.clear();

        setTimeout(function() {
            window.location.href = '/';
        }, 1500);
    });
});
