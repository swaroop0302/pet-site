/* ... (keep other sections styles the same) ... */

/*-----------------------------------*\
    #PARALLAX JAVASCRIPT
\*-----------------------------------*/
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const heroBg = document.querySelector('.hero-bg');
    let scrollPosition = 0;

    function updateParallax() {
        scrollPosition = window.pageYOffset;
    heroBg.style.transform = `translateY(${scrollPosition * 0.5}px)`;
    requestAnimationFrame(updateParallax);
    }

    updateParallax();
});
</script>