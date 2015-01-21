$(document).ready(function() {
	checkArrows(0);

    $('#leftArrow').click(function() {
        backwardsImage();
    });

    $('#rightArrow').click(function() {
        forwardImage();
    });

    function currentImageKey() {
        i = jQuery.inArray($('#page').attr('src'), images);
        return i;
    }

    function backwardsImage() {
        currentImageKey();
        if (i == 0) {
            //changeImage(images.length - 1);
        } else {
            changeImage(i - 1);
        }
        checkArrows(i-1);
    }

    function forwardImage() {
        currentImageKey();
        if (i < images.length - 1) {
            changeImage(i + 1);
        } else {
            //changeImage(0);
        }
        checkArrows(i+1) ;
    }

    function checkArrows(i) {
        if (i == 0) {
            $('#leftArrow').css('display', 'none');
            $('#rightArrow').css('display', 'inline');
        } else if (i == images.length - 1) {
            $('#rightArrow').css('display', 'none');
            $('#leftArrow').css('display', 'inline');
        } else {
            $('#rightArrow').css('display', 'inline');
            $('#leftArrow').css('display', 'inline');
        }            
    }

    function changeImage(i) {
        $('#page').stop().animate({
            opacity: 0,
        }, 200, function() {
            $('#page').attr('src', images[i]);
            $('#slideshow img').load(function() {
                $('#page').stop().animate({
                    opacity: 1,
                }, 200)
            })
        })
    }

});
