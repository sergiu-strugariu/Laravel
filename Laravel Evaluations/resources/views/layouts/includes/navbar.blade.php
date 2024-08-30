<header class="main-header">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        {{--<span class="logo-mini">EUCOM</span>--}}
        <img src="{{ asset('assets/img/favicon/favicon.png') }}" class="logo-mini">
        <!-- logo for regular state and mobile devices -->
        <img src="{{ asset('assets/img/logo-200x65.png') }}" class="logo-lg">
    </a>

    <!-- Header Navbar: style can be found in header.less -->

    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" id="menu-button" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <form action="{{ '/project/search/tasks' }}" method="get" class="sidebar-form">
            <div class="input-group">
                <span class="input-group-btn">
                    <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                      <i class="fa fa-search"></i>
                    </button>
                 </span>
                <input type="text" name="q" class="search-bar" placeholder="Search..."
                       value="{{ Request::query('q') }}">
            </div>
        </form>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                @guest
                    <li>
                        <a href="{{ route('login') }}">
                            <i class="fa fa-sign-in"></i>
                            Login
                        </a>
                    </li>

                @else
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <span class="hidden-xs">{{ Auth::user()->first_name . " " . Auth::user()->last_name }}</span>
                            <span class="ion-arrow-down-b"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                {{--<img src="{{ asset('assets/img/user2-160x160.jpg') }}" class="img-circle"--}}
                                     {{--alt="User Image">--}}

                                <p>
                                    {{ Auth::user()->email }}
                                    <small>Member since {{ Auth::user()->created_at->format('F Y') }}</small>
                                </p>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="pull-left">
                                    <a href="/user/profile" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();"
                                       class="btn btn-default btn-flat">
                                        Sign out
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </li>
                            <!-- Menu Footer-->
                            {{--<li class="user-footer"></li>--}}
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>
</header>
@if(Auth()->user() && Auth()->user()->verified != 0)
    <aside class="main-sidebar">
        <!-- sidebar -->
        <section class="sidebar">

        <?php
        $path = explode('/', Request::path());
        ?>

        <!-- sidebar menu -->
            <ul class="sidebar-menu" data-widget="tree">
                @canAtLeast('user.create')
                {{--<li class="treeview {{ (in_array('admin', $path) && in_array('roles', $path)) && count($path) == 3 && !in_array('client',$path) && !in_array('assessor',$path)? 'active' : '' }}">--}}
                    {{--<a href="#">--}}
                        {{--<i class="fa fa-users"></i> <span>Users</span>--}}
                        {{--<span class="pull-right-container">--}}
                          {{--<i class="fa fa-angle-left pull-right"></i>--}}
                        {{--</span>--}}
                    {{--</a>--}}
                    {{--<ul class="treeview-menu" style="">--}}
                        {{--<li class="{{ in_array('css', $path)  ? 'active' : '' }}"><a href="/admin/roles/css">CSS/Recruiters</a>--}}
                        {{--</li>--}}
                        {{--<li class="{{ in_array('tds', $path) ? 'active' : '' }}"><a href="/admin/roles/tds">Tds</a></li>--}}
                    {{--</ul>--}}
                {{--</li>--}}

                <li class="{{ in_array('client', $path) ? 'active' : '' }}"><a href="/admin/roles/client"><i
                                class="fa fa-users"></i> <span>Clients</span></a>
                </li>
                <li class="{{ in_array('assessor', $path) ? 'active' : '' }}"><a href="/admin/roles/assessor"><i
                                class="fa fa-users"></i> <span>Assessors</span></a>
                </li>
                @endCanAtLeast

                @canAtLeast(['menu.view_projects'])
                <li class="{{ in_array('projects', $path) ? 'active' : '' }}">
                    <a href="/project/projects"><i class="fa fa-book"></i>
                        <span>Projects</span>
                    </a>
                </li>
                @endCanAtLeast


                @hasRole(['master', 'administrator'])
                <li class="treeview billing {{
                (
                    (in_array('billing', $path)) ||
                    (in_array('invoices', $path))
                )
                   ? 'active' : ''}}">
                    <a href="#">
                        <i class="fa fa-money"></i><span>Billing</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" style="">
                        <li class="{{ in_array('billing', $path) ? 'active' : '' }}">
                            <a href="/billing"><i class="fa fa-file-o"></i>
                                <span>Create Invoice</span>
                            </a>
                        </li>

                        <li class="{{ in_array('invoices', $path) ? 'active' : '' }}">
                            <a href="/invoices"><i class="fa fa-files-o"></i>
                                <span>View Invoices</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endHasRole

                @canAtLeast(['menu.view_projects'])
                <li class="{{ in_array('tasks', $path) ? 'active' : '' }}">
                    <a class="a-override" href="/tasks?all=true"><i class="fa fa-tasks"></i>
                        <span>Tasks
                            @hasRole(['master','administrator'])
                            <span class="floating-links">
                                <small class="label pull-right bg-yellow"><span class="a-link" href="/tasks?all=true&project_type=3">ER</span></small>
                                <small class="label pull-right bg-green"><span class="a-link" href="/tasks?all=true&project_type=2">CI</span></small>
                                <small class="label pull-right bg-red"><span class="a-link" href="/tasks?all=true&project_type=1">LA</span></small>
                            </span>
                            @endHasRole
                        </span>
                    </a>
                </li>
                @endCanAtLeast
                @hasRole(['master', 'administrator'])
                <li class="{{ isset($path[1]) && $path[1] == 'tests' ? 'active' : '' }}">
                    <a href="/admin/tests/list"><i class="fa fa-pencil-square-o"></i>
                        <span>Tests</span>
                    </a>
                </li>
                @endHasRole
                @hasRole(['master', 'administrator'])
                <li class="{{ in_array('results', $path) ? 'active' : '' }}">
                    <a href="/results"><i class="fa fa-check-square-o"></i>
                        <span>Test Results</span>
                    </a>
                </li>
                @endHasRole
                @hasRole(['master', 'administrator'])
                <li class="{{ in_array('item-statistics', $path) ? 'active' : '' }}">
                    <a href="/item-statistics"><i class="fa fa-check-square-o"></i>
                        <span>Item Statistics</span>
                    </a>
                </li>
                @endHasRole
                @hasRole(['master', 'administrator'])
                <li class="{{ in_array('cefr', $path) ? 'active' : '' }}">
                    <a href="{{ route('cefr') }}"><i class="fa fa-book"></i>
                        <span>CEFR</span>
                    </a>
                </li>
                @endHasRole

                @hasRole(['master', 'administrator'])
                <li class="{{ in_array('mails', $path) ? 'active' : '' }}">
                    <a href="/admin/mails"><i class="fa fa-envelope"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li class="{{ in_array('logs', $path) ? 'active' : '' }}">
                    <a href="/admin/logs/getAll"><i class="fa fa-history"></i>
                        <span>Logs</span>
                    </a>
                </li>
                @endHasRole

                @canAtLeast('user.create')
                <li class="treeview cruds {{
                ((in_array('create', $path) && in_array('manual', $path)) ||
                  (in_array('create', $path) && in_array('automatic', $path)) ||
                  (in_array('admin', $path) && in_array('roles', $path) && count($path) != 3) ||
                  (in_array('admin', $path) && in_array('permissions', $path)) ||
                  (in_array('admin', $path) && in_array('projectTypes', $path)) ||
                  (in_array('admin', $path) && in_array('usersCrud', $path)))
                   ? 'active' : ''}}">
                    <a href="#">
                        <i class="fa fa-cog"></i><span>Admin</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu" style="">
                        <li class="{{ (in_array('create', $path) && in_array('manual', $path)) ? 'active' : ''}}"><a
                                    href="/admin/create/manual"><i class="fa fa-plus"></i>Create manually</a>
                        </li>
                        <li class="{{ (in_array('create', $path) && in_array('automatic', $path)) ? 'active' : ''}}"><a
                                    href="/admin/create/automatic"><i class="fa fa-plus"></i>Create automatically</a>
                        </li>
                        <li class="{{ (in_array('admin', $path) && in_array('roles', $path) && count($path) == 2 ) ? 'active' : ''}}">
                            <a href="/admin/roles"><i class="fa fa-id-card-o"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                        <li class="{{ (in_array('admin', $path) && in_array('permissions', $path)) ? 'active' : ''}}">
                            <a href="/admin/permissions"><i class="fa fa-exclamation-triangle"></i>
                                <span>Permissions</span>
                            </a>
                        </li>

                        <li class="{{ (in_array('admin', $path) && in_array('projectTypes', $path)) ? 'active' : ''}}">
                            <a href="/admin/projectTypes"><i class="fa fa-stack-exchange"></i>
                                <span>Project Types</span>
                            </a>
                        </li>

                        <li class="{{ (in_array('admin', $path) && in_array('languages', $path)) ? 'active' : ''}}">
                            <a href="/admin/languages"><i class="fa fa-language"></i>
                                <span>Languages</span>
                            </a>
                        </li>

                        <li class="{{ (in_array('admin', $path) && in_array('prices', $path)) ? 'active' : ''}}">
                            <a href="/admin/prices"><span class="pricing-icon"></span>
                                <span>Prices</span>
                            </a>
                        </li>

                        <li class="{{ (in_array('admin', $path) && in_array('tests', $path)) ? 'active' : ''}}">
                            <a href="/admin/tests"><i class="fa fa-stack-exchange"></i>
                                <span>Tests</span>
                            </a>
                        </li>

                        <li class="{{ (in_array('admin', $path) && in_array('task-updates', $path)) ? 'active' : ''}}">
                            <a href="/admin/task-updates"><i class="fa fa-stack-exchange"></i>
                                <span>Task Updates</span>
                            </a>
                        </li>

                        <li class="{{ (in_array('admin', $path) && in_array('usersCrud', $path)) ? 'active' : ''}}">
                            <a href="/admin/usersCrud"><i class="fa fa-users"></i>
                                <span>Users</span>
                            </a>
                        </li>

                        <li class="{{ (in_array('admin', $path) && in_array('settings', $path)) ? 'active' : ''}}">
                            <a href="/admin/settings"><i class="fa fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endCanAtLeast
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
@endif

