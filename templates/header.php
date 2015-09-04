<?php use Roots\Sage\Nav as Nav; ?>

<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-menu-wrapper" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php bloginfo('url'); ?>">
        LOGO
      </a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="nav-menu-wrapper">
      <?php
        wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'walker' => new Nav\NavWalker(),
          'menu_class' => 'nav navbar-nav navbar-right primary-navigation',
        ]);
      ?>
    </div><!-- /.navbar-collapse -->
  </div>
</nav>
