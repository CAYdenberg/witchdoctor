<?php
    use WD\Lib\Nav as Nav;
?>

<nav class="navbar navbar-dark bg-inverse" role="naviagtion" id="primary-navbar">
  <div class="container">

    <a class="navbar-brand" href="<?php bloginfo('url'); ?>">LOGO</a>

    <button class="navbar-toggler hidden-sm-up pull-right" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar2">
      &#9776;
    </button>

    <div class="clearfix hidden-sm-up"></div>

    <div class="collapse navbar-toggleable-xs" id="exCollapsingNavbar2">
      <?php if (has_nav_menu('primary_navigation')) :
        wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class' => 'nav navbar-nav pull-right',
          'walker' => new Nav\NavWalker()
        ]);
      endif; ?>
    </div>

  </div><!-- /.container-fluid -->
</nav>
