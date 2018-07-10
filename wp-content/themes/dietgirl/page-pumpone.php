<?php
/*
Template Name: Pumpone App
*/
$user = wp_get_current_user();
$token = get_user_meta( $user->ID, "token_pumpone", true );
PC::debug([ "token" => $token ]);

get_header();
$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() ); ?>

<div id="main-content" style="height: 1024px; width: 100%;">
    <div id="fitnessbuilder-private-label-embed">
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
          if (h < 1000) h = 1000;
          o.style.height = h + "px";
        }

        var o = document.getElementById("main-content");
        setMoveItHeight(o);

        jQuery( window ).resize(function() {
          setMoveItHeight(o);
        })

        var hasFlash = false;
        try {
          var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
          if(fo) hasFlash = true;
        }catch(e){
          if(navigator.mimeTypes ["application/x-shockwave-flash"] != undefined) hasFlash = true;
        }
        if (!hasFlash) {
            document.write('<div style="text-align: center;"><div style="height: 100px;"><!--  --></div><h3>You do not have Flash player installed.<br><br><a href="http://get.adobe.com/flashplayer/" target="_blank">Click here to download and install Flash.</a></h3></div>');
        } else {
            fitnessBuilderPrivateLabel.embed_sso('16703a8b41fc3102433fcd88e615016c', <?php echo "'" . $token . "'"; ?>, true);
        }
      </script>
    </div>
</div> <!-- #main-content -->

<?php get_footer(); ?>
