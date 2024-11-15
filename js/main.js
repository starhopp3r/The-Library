document.addEventListener('DOMContentLoaded', function() {
    let slideIndex = 1;
    let slideInterval;

    function showSlides(n) {
        let i;
        let slides = document.getElementsByClassName("carousel-slide");
        let dots = document.getElementsByClassName("dot");
        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex-1].style.display = "block";
        dots[slideIndex-1].className += " active";
    }

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    function autoplay() {
        plusSlides(1);
    }

    function startAutoplay() {
        showSlides(slideIndex);
        slideInterval = setInterval(autoplay, 5000); // Change slide every 5 seconds
    }

    function stopAutoplay() {
        clearInterval(slideInterval);
    }

    // Attach event listeners to buttons
    document.querySelector('.prev').addEventListener('click', () => plusSlides(-1));
    document.querySelector('.next').addEventListener('click', () => plusSlides(1));

    // Attach event listeners to dots
    let dots = document.getElementsByClassName("dot");
    for (let i = 0; i < dots.length; i++) {
        dots[i].addEventListener('click', () => currentSlide(i + 1));
    }

    // Start autoplay when the page loads
    startAutoplay();

    // Pause autoplay when user interacts with controls
    document.querySelector('.carousel-container').addEventListener('mouseenter', stopAutoplay);
    document.querySelector('.carousel-container').addEventListener('mouseleave', startAutoplay);
});