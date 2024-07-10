<aside class="left-sidebar bg-sidebar">
    <div id="sidebar" class="sidebar sidebar-with-footer">
      <div class="app-brand">
        <a href="{{ url('/') }}" title="Sleek Dashboard">
          <svg class="brand-icon" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid" width="30" height="33"
            viewBox="0 0 30 33">
            <g fill="none" fill-rule="evenodd">
              <path class="logo-fill-blue" fill="#7DBCFF" d="M0 4v25l8 4V0zM22 4v25l8 4V0z" />
              <path class="logo-fill-white" fill="#FFF" d="M11 4v25l8 4V0z" />
            </g>
          </svg>
          <span class="brand-name text-truncate">Sleek Dashboard</span>
        </a>
      </div>
      <div class="" data-simplebar style="height: 100%;">
        <ul class="nav sidebar-inner" id="sidebar-menu">
          <li class="has-sub">
            <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#dashboard"
              aria-expanded="false" aria-controls="dashboard">
              <i class="mdi mdi-view-dashboard-outline"></i>
              <span class="nav-text">Dashboard</span> <b class="caret"></b>
            </a>
            <ul class="collapse" id="dashboard" data-parent="#sidebar-menu">
              <div class="sub-menu">
                <li class="active">
                  <a class="sidenav-item-link" href="{{ url('/') }}">
                    <span class="nav-text">Ecommerce</span>
                  </a>
                </li>
                <li>
                  <a class="sidenav-item-link" href="#">
                    <span class="nav-text">Analytics</span>
                    <span class="badge badge-success">new</span>
                  </a>
                </li>
              </div>
            </ul>
          </li>
          <!-- Add other menu items here -->
        </ul>
      </div>
      <div class="sidebar-footer">
        <hr class="separator mb-0" />
        <div class="sidebar-footer-content">
          <h6 class="text-uppercase">
            Cpu Uses <span class="float-right">40%</span>
          </h6>
          <div class="progress progress-xs">
            <div class="progress-bar active" style="width: 40%;" role="progressbar"></div>
          </div>
          <h6 class="text-uppercase">
            Memory Uses <span class="float-right">65%</span>
          </h6>
          <div class="progress progress-xs">
            <div class="progress-bar progress-bar-warning" style="width: 65%;" role="progressbar"></div>
          </div>
        </div>
      </div>
    </div>
  </aside>
  