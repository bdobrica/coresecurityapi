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
                        	<!--li>
                                <a href="#">
									<span class="icon blue"><i class="fa fa-user"></i></span>
									<span class="message">New user registration</span>
									<span class="time">1 min</span> 
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="icon green"><i class="fa fa-comment-o"></i></span>
									<span class="message">New comment</span>
									<span class="time">7 min</span> 
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="icon green"><i class="fa fa-comment-o"></i></span>
									<span class="message">New comment</span>
									<span class="time">8 min</span> 
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="icon green"><i class="fa fa-comment-o"></i></span>
									<span class="message">New comment</span>
									<span class="time">16 min</span> 
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="icon blue"><i class="fa fa-user"></i></span>
									<span class="message">New user registration</span>
									<span class="time">36 min</span> 
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="icon yellow"><i class="fa fa-shopping-cart"></i></span>
									<span class="message">2 items sold</span>
									<span class="time">1 hour</span> 
                                </a>
                            </li>
							<li class="warning">
                                <a href="#">
									<span class="icon red"><i class="fa fa-user"></i></span>
									<span class="message">User deleted account</span>
									<span class="time">2 hour</span> 
                                </a>
                            </li>
							<li class="warning">
                                <a href="#">
									<span class="icon red"><i class="fa fa-shopping-cart"></i></span>
									<span class="message">Transaction was canceled</span>
									<span class="time">6 hour</span> 
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="icon green"><i class="fa fa-comment-o"></i></span>
									<span class="message">New comment</span>
									<span class="time">yesterday</span> 
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="icon blue"><i class="fa fa-user"></i></span>
									<span class="message">New user registration</span>
									<span class="time">yesterday</span> 
                                </a>
                            </li-->>
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
							<!--li>
                                <a href="#">
									<span class="header">
										<span class="title">iOS Development</span>
										<span class="percent"></span>
									</span>
                                    <div class="taskProgress progressSlim progressBlue">80</div> 
                                </a>
                            </li>
                            <li>
                                <a href="#">
									<span class="header">
										<span class="title">Android Development</span>
										<span class="percent"></span>
									</span>
                                    <div class="taskProgress progressSlim progressYellow">47</div> 
                                </a>
                            </li>
                            <li>
                                <a href="#">
									<span class="header">
										<span class="title">Django Project For Google</span>
										<span class="percent"></span>
									</span>
                                    <div class="taskProgress progressSlim progressRed">32</div> 
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="header">
										<span class="title">SEO for new sites</span>
										<span class="percent"></span>
									</span>
                                    <div class="taskProgress progressSlim progressGreen">63</div> 
                                </a>
                            </li>
                            <li>
                                <a href="#">
									<span class="header">
										<span class="title">New blog posts</span>
										<span class="percent"></span>
									</span>
                                    <div class="taskProgress progressSlim progressPink">80</div> 
                                </a>
                            </li-->
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
                        	<!--li>
                                <a href="#">
									<span class="avatar"><img src="assets/img/avatar.jpg" alt="Avatar"></span>
									<span class="header">
										<span class="from">
									    	Łukasz Holeczek
									     </span>
										<span class="time">
									    	6 min
									    </span>
									</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>  
                                </a>
                            </li>
                            <li>
                                <a href="#">
									<span class="avatar"><img src="assets/img/avatar2.jpg" alt="Avatar"></span>
									<span class="header">
										<span class="from">
									    	Megan Abott
									     </span>
										<span class="time">
									    	56 min
									    </span>
									</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>  
                                </a>
                            </li>
                            <li>
                                <a href="#">
									<span class="avatar"><img src="assets/img/avatar3.jpg" alt="Avatar"></span>
									<span class="header">
										<span class="from">
									    	Kate Ross
									     </span>
										<span class="time">
									    	3 hours
									    </span>
									</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>  
                                </a>
                            </li>
							<li>
                                <a href="#">
									<span class="avatar"><img src="assets/img/avatar4.jpg" alt="Avatar"></span>
									<span class="header">
										<span class="from">
									    	Julie Blank
									     </span>
										<span class="time">
									    	yesterday
									    </span>
									</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>  
                                </a>
                            </li>
                            <li>
                                <a href="#">
									<span class="avatar"><img src="assets/img/avatar5.jpg" alt="Avatar"></span>
									<span class="header">
										<span class="from">
									    	Jane Sanders
									     </span>
										<span class="time">
									    	Jul 25, 2012
									    </span>
									</span>
                                    <span class="message">
                                        Lorem ipsum dolor sit amet consectetur adipiscing elit, et al commore
                                    </span>  
                                </a>
                            </li-->
							<li>
                        		<a class="dropdown-menu-sub-footer">Vezi toate mesajele</a>
							</li>	
						</ul>
					</li>
					<!-- end: Message Dropdown -->
					<li class="dropdown hidden-xs">
						<a class="btn candy-toggle" href="#">
							<i class="fa fa-comment"></i>
							<span class="number"></span>
						</a>
					</li>
					<li>
						<a class="btn" href="/user">
							<i class="fa fa-wrench"></i>
						</a>
					</li>
					<!-- end: Message Dropdown -->
					<!-- start: User Dropdown -->
					<li class="dropdown">
						<a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
							<div class="avatar"><img src="<?php echo $wp_crm_user->get ('avatar', 'c40x40'); ?>" alt="Avatar"></div>
							<div class="user">
								<span class="hello">Bine ai venit!</span>
								<span class="name"><?php echo $wp_crm_user->get ('name'); ?></span>
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
			<div id="sidebar-left" class="col-lg-2 col-sm-1 minified">

				<div class="sidebar-nav nav-collapse collapse navbar-collapse">
					<?php $wp_crm_menu->set ('render', WP_CRM_Menu::WP_CRM_Menu_List);
					$view = new WP_CRM_View ($wp_crm_menu);
					unset ($view); ?>
				</div>
				<a href="#" id="main-menu-min" class="full visible-md visible-lg"><i class="fa fa-angle-double-left"></i></a>
			</div>
			<!-- end: Main Menu -->

			<!-- start: Content -->
			<div id="content" class="col-lg-10 col-sm-11 sidebar-minified">

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