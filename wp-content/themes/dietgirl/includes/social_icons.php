<ul class="et-social-icons">

<?php if ( 'on' === et_get_option( 'divi_show_facebook_icon', 'on' ) ) : ?>
  <li class="et-social-icon et-social-facebook">
    <a href="<?php echo esc_url( et_get_option( 'divi_facebook_url', '#' ) ); ?>" class="icon">
      <span><?php esc_html_e( 'Facebook', 'Divi' ); ?></span>
    </a>
  </li>
<?php endif; ?>
<?php if ( 'on' === et_get_option( 'divi_show_twitter_icon', 'on' ) ) : ?>
  <li class="et-social-icon et-social-twitter">
    <a href="<?php echo esc_url( et_get_option( 'divi_twitter_url', '#' ) ); ?>" class="icon">
      <span><?php esc_html_e( 'Twitter', 'Divi' ); ?></span>
    </a>
  </li>
<?php endif; ?>
<?php if ('on' === et_get_option('divi_show_instagram_icon', 'on')) : ?>
    <li class="et-social-icon et-social-instagram">
        <a href="<?php echo esc_url(et_get_option('divi_instagram_url', 'https://www.instagram.com/dietgirlfitness/')); ?>" class="icon">
            <span><?php esc_html_e('Instagram', 'Divi'); ?></span>
        </a>
    </li>
<?php endif; ?>
<?php if ( 'on' === et_get_option( 'divi_show_pinterest_icon', 'on' ) ) : ?>
  <li class="et-social-icon et-social-pinterest">
    <a href="<?php echo esc_url( et_get_option( 'divi_pinterest_url', '#' ) ); ?>" class="icon">
      <span><?php esc_html_e( 'Pinterest', 'Divi' ); ?></span>
    </a>
  </li>
<?php endif; ?>
<?php if ( 'on' === et_get_option( 'divi_show_google_icon', 'on' ) ) : ?>
  <li class="et-social-icon et-social-google-plus">
    <a href="<?php echo esc_url( et_get_option( 'divi_google_url', '#' ) ); ?>" class="icon">
      <span><?php esc_html_e( 'Google', 'Divi' ); ?></span>
    </a>
  </li>
<?php endif; ?>
<?php if ( 'on' === et_get_option( 'divi_show_rss_icon', 'on' ) ) : ?>
<?php
  $et_rss_url = '' !== et_get_option( 'divi_rss_url' )
    ? et_get_option( 'divi_rss_url' )
    : get_bloginfo( 'rss2_url' );
?>
  <li class="et-social-icon et-social-rss">
    <a href="<?php echo esc_url( $et_rss_url ); ?>" class="icon">
      <span><?php esc_html_e( 'RSS', 'Divi' ); ?></span>
    </a>
  </li>
<?php endif; ?>

<li class="et-social-icon et-social-linkedin">
<a href="https://www.linkedin.com/company/dietgirl/‎" class="icon">
<span><?php esc_html_e( 'Linkedin', 'Divi' ); ?></span>
</a>
</li>

 
</ul>