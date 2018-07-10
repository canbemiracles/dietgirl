<?php
/*
Template Name: Vitabot App
*/
$user = wp_get_current_user();
$password = get_user_meta( $user->ID, 'mafteaj', true );
get_header();
$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>

<div id="main-content" class="vitabot" style="height: 800px; width: 100%;">
  <?php echo '<iframe style="height: 100%; width: 100%;" src="http://mealplan.dietgirl.com/members/login/?username=' . $user->user_login . '&password=' . $password . '"></iframe>'; ?>
</div> <!-- #main-content -->

<script>

  function getViewportHeight () {
    if (window.innerHeight != window.undefined)
      return window.innerHeight;
    if (document.compatMode == 'CSS1Compat')
      return document.documentElement.clientHeight;
    if (document.body)
      return document.body.clientHeight;
    return window.undefined;
  }

  function getTop (obj) {
    return (obj.offsetParent == null ? obj.offsetTop : obj.offsetTop + getTop(obj.offsetParent));
  }

  function setMoveItHeight(o) {
    var h = getViewportHeight() - getTop(o) - 10;
    if (h < 500) h = 500;
    o.style.height = h + "px";
  }

  var o = document.getElementById("main-content");
  setMoveItHeight(o);

  jQuery( window ).resize(function() {
    setMoveItHeight(o);
  })
</script>

<?php get_footer(); ?>
