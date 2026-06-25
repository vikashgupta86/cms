/**
 * Gallery Page JavaScript
 * Handles filtering, lightbox, and animations
 */

$(document).ready(function() {
    // Filter functionality
    $(".filter-button").click(function() {
        const value = $(this).attr("data-filter");
        
        // Update active button
        $(".filter-button").removeClass("active");
        $(this).addClass("active");
        
        // Filter items
        if (value === "all") {
            $(".gallery-item").fadeIn(400);
            checkEmptyState();
        } else {
            $(".gallery-item").hide();
            $(".gallery-item[data-category='" + value + "']").fadeIn(400);
            checkEmptyState();
        }
    });

    // Check if gallery is empty
    function checkEmptyState() {
        setTimeout(function() {
            const visibleItems = $(".gallery-item:visible").length;
            if (visibleItems === 0) {
                $("#emptyState").addClass("active");
            } else {
                $("#emptyState").removeClass("active");
            }
        }, 500);
    }

    // Lightbox functionality
    $(".gallery-card").click(function(e) {
        const $card = $(this);
        const $video = $card.find("video");
        const $image = $card.find(".gallery-image");
        
        if ($video.length) {
            // Handle video
            const videoSrc = $video.find("source").attr("src");
            $("#lightboxVideo").find("source").attr("src", videoSrc);
            $("#lightboxVideo")[0].load();
            $("#lightboxVideo").show();
            $("#lightboxImage").hide();
        } else {
            // Handle image
            const imageSrc = $image.attr("src");
            $("#lightboxImage").attr("src", imageSrc);
            $("#lightboxImage").show();
            $("#lightboxVideo").hide();
        }
        
        $("#lightboxModal").addClass("active");
        $("body").css("overflow", "hidden");
    });

    // Close lightbox
    $("#lightboxClose, #lightboxModal").click(function(e) {
        if (e.target === this) {
            $("#lightboxModal").removeClass("active");
            $("body").css("overflow", "auto");
            
            // Pause video if playing
            const video = $("#lightboxVideo")[0];
            if (video) {
                video.pause();
            }
        }
    });

    // Close on Escape key
    $(document).keydown(function(e) {
        if (e.key === "Escape") {
            $("#lightboxModal").removeClass("active");
            $("body").css("overflow", "auto");
            
            const video = $("#lightboxVideo")[0];
            if (video) {
                video.pause();
            }
        }
    });

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const image = entry.target;
                    image.src = image.dataset.src || image.src;
                    image.classList.add('loaded');
                    imageObserver.unobserve(image);
                }
            });
        });

        document.querySelectorAll('.gallery-image, .gallery-video').forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // Add staggered animation on scroll
    function revealOnScroll() {
        const items = document.querySelectorAll('.gallery-item');
        items.forEach((item, index) => {
            const itemTop = item.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (itemTop < windowHeight - 100) {
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1)';
                }, index * 50);
            }
        });
    }

    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); // Initial check
});

