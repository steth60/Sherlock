<nav class="navbar navbar-static-top navbar-expand-lg">
    <!-- Sidebar toggle button -->
    <button id="sidebar-toggler" class="sidebar-toggle">
      <span class="sr-only">Toggle navigation</span>
    </button>
    <!-- search form -->
    <div class="search-form d-none d-lg-inline-block">
 
    </div>
    <div class="navbar-right">
      <ul class="nav navbar-nav">


        <!-- User Account -->
        <li class="dropdown user-menu">
          <button href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
            
            <span class="d-none d-lg-inline-block">{{ Auth::user()->name }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-right">
            <!-- User image -->
            <li class="dropdown-header">
              <div class="d-inline-block">
                {{ Auth::user()->name }} <small class="pt-1">{{ Auth::user()->email }}</small>
              </div>
            </li>
            <li>
              <a href="{{ route('settings.index') }}" >
                <i class="mdi mdi-account"></i> My setting
              </a>
            </li>
            <li class="dropdown-footer">
              <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mdi mdi-logout"></i> Log Out
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
  
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
  </form>
  