<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
  <?php get_template_part('templates/head'); ?>

  <body <?php body_class(); ?>>
    <!--[if lt IE 9]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage'); ?>
      </div>
    <![endif]-->
    <?php
      do_action('get_header');
      get_template_part('templates/header');
    ?>
    <?php include Wrapper\template_path(); ?>

    <?php
      do_action('get_footer');
      get_template_part('templates/footer');
    ?>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.1.min.js"></script>
    <script>
      if (typeof jQuery === "undefined") {
        document.write('<script type="text/javascript" src="<?= get_template_directory_uri(); ?>/bower_components/jquery/dist/jquery.min.js"><\/script>');
      }
    </script>

    <?php
      wp_footer();
    ?>

  </body>
</html>
