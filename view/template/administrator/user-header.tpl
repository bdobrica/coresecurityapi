		<!-- start: Header -->
	<header class="navbar">
		<div class="container">
			<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".sidebar-nav.nav-collapse">
			      <span class="icon-bar"></span>
			      <span class="icon-bar"></span>
			      <span class="icon-bar"></span>
			</button>
			<a id="main-menu-toggle" class="hidden-xs open"><i class="fa fa-bars"></i></a>		
			<a class="navbar-brand col-md-2 col-sm-1 col-xs-2" href="/"><span><?php bloginfo('name'); ?></span></a>
			<div id="search" class="col-sm-4 col-xs-8 col-lg-3">
				<select>
					<option>cauta</option>
					<option>utilizatori</option>
					<option>companii</option>
					<option>mesaje</option>
					<option>fisiere</option>
					<option>facturi</option>
			  	</select>
				<input type="text" placeholder="cauta" />
				<i class="fa fa-search"></i>
			</div>
			<!-- start: Header Menu -->
			<div class="nav-no-collapse header-nav">
				<ul class="nav navbar-nav pull-right">
					<li class="dropdown hidden-xs">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-warning"></i>
							<span class="number"></span>
						</a>
						<ul class="dropdown-menu notifications">
							<li class="dropdown-menu-title">
								<span>Nu ai nicio notificare.</span>
							</li>	
							<li class="dropdown-menu-sub-footer">
                        					<a>Vezi toate notificarile.</a>
							</li>	
						</ul>
					</li>
					<!-- start: Notifications Dropdown -->
					<li class="dropdown hidden-xs">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-tasks"></i>
							<span class="number"></span>
						</a>
						<ul class="dropdown-menu tasks">
							<li>
								<span class="dropdown-menu-title">Nu ai nicio sarcina in derulare</span>
			                        	</li>
							<li>
			                        		<a class="dropdown-menu-sub-footer">Vezi toate sarcinile</a>
							</li>	
						</ul>
					</li>
					<!-- end: Notifications Dropdown -->
					<!-- start: Message Dropdown -->
					<li class="dropdown hidden-xs">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-envelope"></i>
							<span class="number"></span>
						</a>
						<ul class="dropdown-menu messages">
							<li>
								<span class="dropdown-menu-title">Nu ai mesaje noi</span>
							</li>	
							<li>
								<a class="dropdown-menu-sub-footer">Vezi toate mesajele</a>
							</li>	
						</ul>
					</li>
					<li class="dropdown hidden-xs">
						<a class="btn candy-toggle" href="#">
							<i class="fa fa-comment"></i>
							<span class="number"></span>
						</a>
					</li>
					<!-- end: Message Dropdown -->
					<li>
						<a class="btn" href="/user">
							<i class="fa fa-wrench"></i>
						</a>
					</li>
					<!-- start: User Dropdown -->
					<li class="dropdown">
						<a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
							<div class="avatar"><img src="<?php echo $wp_crm_user->get ('avatar'); ?>" alt="<?php echo $wp_crm_user->get ('name'); ?>"></div>
							<div class="user">
								<span class="hello">Bine ai venit!</span>
								<span class="name"><?php echo $wp_crm_user->get ('first_name'); ?></span>
							</div>
						</a>
						<ul class="dropdown-menu">
							<li><a href="/user"><i class="fa fa-user"></i> Profil</a></li>
							<li><a href="/settings"><i class="fa fa-cog"></i> Settings</a></li>
							<li><a href="/messages"><i class="fa fa-envelope"></i> Messages</a></li>
							<li><a href="#"><i class="fa fa-comment candy-toggle"></i> Chat</a></li>
							<li><a href="<?php echo wp_logout_url('/'); ?>"><i class="fa fa-off"></i> Logout</a></li>
						</ul>
					</li>
					<!-- end: User Dropdown -->
				</ul>
			</div>
			<!-- end: Header Menu -->
			
		</div>	
	</header>
	<!-- end: Header -->
	
		<div class="container">
		<div class="row">
				
			<!-- start: Main Menu -->
			<div id="sidebar-left" class="col-lg-2 col-md-2 col-sm-1 ">

				<div class="sidebar-nav nav-collapse collapse navbar-collapse">
					<?php $wp_crm_menu->set ('render', WP_CRM_Menu::WP_CRM_Menu_List);
					$view = new WP_CRM_View ($wp_crm_menu);
					unset ($view); ?>
				</div>
				<a href="#" id="main-menu-min" class="full visible-md visible-lg"><i class="fa fa-angle-double-left"></i></a>
			</div>
			<!-- end: Main Menu -->

			<!-- start: Content -->
			<div id="content" class="col-lg-10 col-md-10 col-sm-11">
				<div class="row">
					<div class="col-lg-10 col-md-10 col-sm-11">

						<!-- start: BreadCrumbs -->
						<ol class="breadcrumb">
<?php
	$base = "/";
?>
					  		<li><a href="<?php echo $base; ?>"><?php bloginfo ('name'); ?></a></li>
<?php
	$breadcrumbs = WP_CRM_Theme::breadcrumbs ();
	if (!empty ($breadcrumbs))
		foreach ($breadcrumbs as $slug => $title) {
				$base = rtrim ($base, '/') . '/' . $slug;
?>
			  	<li class="active"><a href="<?php echo $base; ?>"><?php echo $title; ?></a></li>
<?php
			}
?>
						</ol>
						<!-- end: BreadCrumbs -->
