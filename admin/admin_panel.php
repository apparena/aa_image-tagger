<script type="text/javascript">
$(function(){
    $('.admin_panel').tabSlideOut({
        tabHandle: '.handle',                     //class of the element that will become your tab
        pathToTabImage: 'img/admin_right.jpg', //path to the image for the tab //Optionally can be set using css
        imageHeight: '122px',                     //height of tab image           //Optionally can be set using css
        imageWidth: '50px',                       //width of tab image            //Optionally can be set using css
        tabLocation: 'right',                      //side of screen where tab lives, top, right, bottom, or left
        speed: 500,                               //speed of animation
        action: 'click',                          //options: 'click' or 'hover', action to trigger animation
        topPos: '0px',                          //position from the top/ use if tabLocation is left or right
        leftPos: '20px',                          //position from left/ use if tabLocation is bottom or top
        fixedPosition: true,                      //options: true makes it stick(fixed position) on scroll
    });
	$('.admin_panel').show();
});
</script>

<div class="admin_panel" style="z-index:2050;">
	<a class="handle" href="admin.php">Admin</a>
	<div class="admin_position">
	<? require_once dirname(__FILE__).'/admin.php'; ?>
	</div>
</div>
