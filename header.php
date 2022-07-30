<!DOCTYPE html>
<html <?php language_attributes() ?>>
  <head>
    <meta charset="<?php bloginfo('charset') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
  </head>
  <body>
    <header class="site-header">
    <div class="container">
      <h1 class="school-logo-text float-left"><a href="<?php echo site_url()?>"><strong>Fictional</strong> University</a></h1>
      <span class="js-search-trigger site-header__search-trigg  er"><i class="fa fa-search" aria-hidden="true"></i></span>
      <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
      <div class="site-header__menu group">
        <nav class="main-navigation">
          <?php 
          wp_nav_menu(array(
            'theme_location' => 'headerMenuLocation'
          ))
          ?>
            <!--
              static menu
          <ul class="min-list group">
          
            <li <?php if(is_page('about-us') or wp_get_post_parent_id(0) == 16 ) echo 'class="current-menu-item"' ?>><a href="<?php echo site_url('/about-us') ?>">About Us</a></li>
            <li><a href="#">Programs</a></li>
            <li><a href="#">Events</a></li>
            <li><a href="#">Campuses</a></li> 
            <li <?php if(get_post_type() == 'post') echo 'class="current-menu-item"' ?>><a href="<?php echo site_url('/blog') ?>">Blog</a></li>
            
          </ul>
            -->
        </nav>
        <div class="site-header__util">
          <?php
          if(is_user_logged_in()){
            ?>
            <a href="<?php echo wp_logout_url() ?>" class="btn btn--small  btn--dark-orange float-left btn--with-photo">
            <span class="site-header__avatar"><?php echo get_avatar(get_current_user_id(), 60) ?></span>
           <span class="btn__text">Log Out</span>
           
          </a>
            <?php

          }
          else{
            ?>
            <a href="<?php echo wp_login_url(); ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
          <a href="<?php echo wp_registration_url(); ?>" class="btn btn--small  btn--dark-orange float-left">Sign Up</a>
            <?php

          }
           ?>
          
          <span class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></span>
        </div>
      </div>
    </div>
  </header>
