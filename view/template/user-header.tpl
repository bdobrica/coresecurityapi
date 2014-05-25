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
					<option>everything</option>
					<option>messages</option>
					<option>comments</option>
					<option>users</option>
			  	</select>
				<input type="text" placeholder="search" />
				<i class="fa fa-search"></i>
			</div>
			<!-- start: Header Menu -->
			<div class="nav-no-collapse header-nav">
				<ul class="nav navbar-nav pull-right">
					<li class="dropdown hidden-xs">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-warning"></i>
							<span class="number">11</span>
						</a>
						<ul class="dropdown-menu notifications">
							<li class="dropdown-menu-title">
								<span>You have 11 notifications</span>
							</li>	
                        	<li>
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
                            </li>
                            <li class="dropdown-menu-sub-footer">
                        		<a>View all notifications</a>
							</li>	
						</ul>
					</li>
					<!-- start: Notifications Dropdown -->
					<li class="dropdown hidden-xs">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-tasks"></i>
							<span class="number">17</span>
						</a>
						<ul class="dropdown-menu tasks">
							<li>
								<span class="dropdown-menu-title">You have 17 tasks in progress</span>
                        	</li>
							<li>
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
                            </li>
							<li>
                        		<a class="dropdown-menu-sub-footer">View all tasks</a>
							</li>	
						</ul>
					</li>
					<!-- end: Notifications Dropdown -->
					<!-- start: Message Dropdown -->
					<li class="dropdown hidden-xs">
						<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
							<i class="fa fa-envelope"></i>
							<span class="number">9</span>
						</a>
						<ul class="dropdown-menu messages">
							<li>
								<span class="dropdown-menu-title">You have 9 messages</span>
							</li>	
                        	<li>
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
                            </li>
							<li>
                        		<a class="dropdown-menu-sub-footer">View all messages</a>
							</li>	
						</ul>
					</li>
					<!-- end: Message Dropdown -->
					<li>
						<a class="btn" href="#">
							<i class="fa fa-wrench"></i>
						</a>
					</li>
					<!-- start: User Dropdown -->
					<li class="dropdown">
						<a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
							<div class="avatar"><img src="https://secure.gravatar.com/<?php echo md5(strtolower(trim($current_user->user_email))); ?>" alt="Avatar"></div>
							<div class="user">
								<span class="hello">Welcome!</span>
								<span class="name"><?php echo $current_user->display_name; ?></span>
							</div>
						</a>
						<ul class="dropdown-menu">
							<li><a href="/user"><i class="fa fa-user"></i> Profil</a></li>
							<li><a href="/settings"><i class="fa fa-cog"></i> Settings</a></li>
							<li><a href="/messages"><i class="fa fa-envelope"></i> Messages</a></li>
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
			<div id="sidebar-left" class="col-lg-2 col-sm-1 ">

				<div class="sidebar-nav nav-collapse collapse navbar-collapse">
					<?php $wp_crm_menu->set ('render', WP_CRM_Menu::WP_CRM_MENU_LIST);
					$view = new WP_CRM_View ($wp_crm_menu);
					unset ($view); ?>
					<!--ul class="nav main-menu">
						<li><a href="/"><i class="fa fa-bar-chart-o"></i><span class="hidden-sm text"> Dashboard</span></a></li>
						<li>
							<a class="dropmenu" href="#"><i class="fa fa-eye"></i><span class="hidden-sm text"> UI Features</span> <span class="chevron closed"></span></a>
							<ul>
								<li><a class="submenu" href="ui-sliders-progress.html"><i class="fa fa-eye"></i><span class="hidden-sm text"> Sliders & Progress</span></a></li>
								<li><a class="submenu" href="ui-nestable-list.html"><i class="fa fa-eye"></i><span class="hidden-sm text"> Nestable Lists</span></a></li>
								<li><a class="submenu" href="ui-elements.html"><i class="fa fa-eye"></i><span class="hidden-sm text"> Elements</span></a></li>
							</ul>
							</li>
						<li><a href="widgets.html"><i class="fa fa-dashboard"></i><span class="hidden-sm text"> Widgets</span></a></li>
						<li>
							<a class="dropmenu" href="#"><i class="fa fa-folder-o"></i><span class="hidden-sm text"> Example Pages</span> <span class="chevron closed"></span></a>
							<ul>
								<li><a class="submenu" href="page-inbox.html"><i class="fa fa-envelope-o"></i><span class="hidden-sm text"> Inbox</span></a></li>
								<li><a class="submenu" href="page-invoice.html"><i class="fa fa-file-text"></i><span class="hidden-sm text"> Invoice</span></a></li>
								<li><a class="submenu" href="page-todo.html"><i class="fa fa-tasks"></i><span class="hidden-sm text"> ToDo & Timeline</span></a></li>
								<li><a class="submenu" href="page-profile.html"><i class="fa fa-male"></i><span class="hidden-sm text"> Profile</span></a></li>
								<li><a class="submenu" href="page-pricing-tables.html"><i class="fa fa-table"></i><span class="hidden-sm text"> Pricing Tables</span></a></li>
								<li><a class="submenu" href="page-404.html"><i class="fa fa-unlink"></i><span class="hidden-sm text"> 404</span></a></li>
								<li><a class="submenu" href="page-500.html"><i class="fa fa-unlink"></i><span class="hidden-sm text"> 500</span></a></li>
								<li><a class="submenu" href="page-lockscreen.html"><i class="fa fa-lock"></i><span class="hidden-sm text"> LockScreen</span></a></li>
								<li><a class="submenu" href="page-lockscreen2.html"><i class="fa fa-lock"></i><span class="hidden-sm text"> LockScreen2</span></a></li>
								<li><a class="submenu" href="page-login.html"><i class="fa fa-key"></i><span class="hidden-sm text"> Login Page</span></a></li>
								<li><a class="submenu" href="page-register.html"><i class="fa fa-sign-in"></i><span class="hidden-sm text"> Register Page</span></a></li>
							</ul>	
						</li>
						<li>
							<a class="dropmenu" href="#"><i class="fa fa-edit"></i><span class="hidden-sm text"> Forms</span> <span class="chevron closed"></span></a>
							<ul>
								<li><a class="submenu" href="form-elements.html"><i class="fa fa-edit"></i><span class="hidden-sm text"> Form elements</span></a></li>
								<li><a class="submenu" href="form-wizard.html"><i class="fa fa-edit"></i><span class="hidden-sm text"> Wizard</span></a></li>
								<li><a class="submenu" href="form-dropzone.html"><i class="fa fa-edit"></i><span class="hidden-sm text"> Dropzone Upload</span></a></li>
								<li><a class="submenu" href="form-x-editable.html"><i class="fa fa-edit"></i><span class="hidden-sm text"> X-editable</span></a></li>
							</ul>
						</li>
						<li>
							<a class="dropmenu" href="#"><i class="fa fa-list-alt"></i><span class="hidden-sm text"> Charts</span> <span class="chevron closed"></span></a>
							<ul>
								<li><a class="submenu" href="charts-flot.html"><i class="fa fa-chevron-right"></i><span class="hidden-sm text"> Flot Charts</span></a></li>
								<li><a class="submenu" href="charts-xcharts.html"><i class="fa fa-chevron-right"></i><span class="hidden-sm text"> xCharts</span></a></li>
								<li><a class="submenu" href="charts-others.html"><i class="fa fa-chevron-right"></i><span class="hidden-sm text"> Other</span></a></li>
							</ul>
						
						</li>
						<li><a href="typography.html"><i class="fa fa-font"></i><span class="hidden-sm text"> Typography</span></a></li>
						<li><a href="gallery.html"><i class="fa fa-picture-o"></i><span class="hidden-sm text"> Gallery</span></a></li>
						<li><a href="table.html"><i class="fa fa-align-justify"></i><span class="hidden-sm text"> Tables</span></a></li>
						<li><a href="calendar.html"><i class="fa fa-calendar"></i><span class="hidden-sm text"> Calendar</span></a></li>
						<li><a href="file-manager.html"><i class="fa fa-folder-open"></i><span class="hidden-sm text"> File Manager</span></a></li>
						<li>
							<a class="dropmenu" href="#"><i class="fa fa-star"></i><span class="hidden-sm text"> Icons</span> <span class="chevron closed"></span></a>
							<ul>
								<li><a class="submenu" href="icons-halflings.html"><i class="fa fa-star"></i><span class="hidden-sm text"> Halflings</span></a></li>
								<li><a class="submenu" href="icons-glyphicons-pro.html"><i class="fa fa-star"></i><span class="hidden-sm text"> Glyphicons PRO</span></a></li>
								<li><a class="submenu" href="icons-filetypes.html"><i class="fa fa-star"></i><span class="hidden-sm text"> Filetypes</span></a></li>
								<li><a class="submenu" href="icons-social.html"><i class="fa fa-star"></i><span class="hidden-sm text"> Social</span></a></li>
								<li><a class="submenu" href="icons-font-awesome.html"><i class="fa fa-star"></i><span class="hidden-sm text"> Font Awesome</span></a></li>
							</ul>
						</li>
						<li>
							<a class="dropmenu" href="#"><i class="fa fa-folder-open"></i><span class="hidden-sm text"> 4 Level Menu</span> <span class="chevron closed"></span></a>
							<ul>
								<li><a href="2nd-level.html"><i class="fa fa-folder"></i><span class="hidden-sm text"> 2nd Level</span></a></li>
								<li>
									<a class="dropmenu" href="#"><i class="fa fa-folder-open"></i><span class="hidden-sm text"> 2nd Level</span> <span class="chevron closed"></span></a>
									<ul>
										<li><a href="3rd-level.html"><i class="fa fa-folder"></i><span class="hidden-sm text"> 3rd Level</span></a></li>
										<li>
											<a class="dropmenu" href="#"><i class="fa fa-folder-open"></i><span class="hidden-sm text"> 3rd Level</span> <span class="chevron closed"></span></a>
											<ul>
												<li>
													<a class="submenu" href="4th-level.html"><i class="fa fa-folder"></i><span class="hidden-sm text"> 4th Level</span></a>
												</li>
											</ul>
										</li>
										<li>
											<a class="dropmenu" href="#"><i class="fa fa-folder-open"></i><span class="hidden-sm text"> 3rd Level</span> <span class="chevron closed"></span></a>
											<ul>
												<li>
													<a class="submenu" href="4th-level2.html"><i class="fa fa-folder"></i><span class="hidden-sm text"> 4th Level</span></a>
												</li>
											</ul>
										</li>
									</ul>	
								</li>
							</ul>
						</li>
					</ul-->
				</div>
									<a href="#" id="main-menu-min" class="full visible-md visible-lg"><i class="fa fa-angle-double-left"></i></a>
							</div>
			<!-- end: Main Menu -->

			<!-- start: Content -->
			<div id="content" class="col-lg-10 col-sm-11 ">
