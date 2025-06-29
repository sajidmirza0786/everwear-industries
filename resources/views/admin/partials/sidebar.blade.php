<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            {{-- You can uncomment this if you have a logo image --}}
            <img src="{{ url(\Storage::url(settings()->logo??'')) }}" width="80px" alt="On Jewel Logo">
            {{-- <h5 class="app-brand-text demo menu-text fw-bolder ms-2">On Jewel</h5> --}}
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-alt"></i> {{-- Changed to a more general home icon --}}
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        @if(auth()->user()->user_type === "admin")
        <li class="menu-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i> {{-- Icon for settings --}}
                <div data-i18n="Settings">Settings</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Categories">Customers</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" class="menu-link">
                        <div data-i18n="View users">View Customers</div> {{-- More descriptive text --}}
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.users.create') || request()->routeIs('admin.users.edit') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.create') }}" class="menu-link">
                        <div data-i18n="Add New Category">Add New Customer</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Catalogue</span>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-sitemap"></i> {{-- More specific icon for categories/hierarchy --}}
                <div data-i18n="Categories">Categories</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}" class="menu-link">
                        <div data-i18n="View Categories">View Categories</div> {{-- More descriptive text --}}
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.categories.create') || request()->routeIs('admin.categories.edit') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.create') }}" class="menu-link">
                        <div data-i18n="Add New Category">Add New Category</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.products.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-diamond"></i> 
                <div data-i18n="Products">Products</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.products.index') }}" class="menu-link">
                        <div data-i18n="View Products">View Products</div> 
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.products.create') || request()->routeIs('admin.products.edit') ? 'active' : '' }}">
                    <a href="{{ route('admin.products.create') }}" class="menu-link">
                        <div data-i18n="Add New Product">Add New Product</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Orders</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <a href="{{ route('admin.orders.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div data-i18n="Dashboard">Orders</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Others</span>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.shippingcharges.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-ship"></i> 
                <div data-i18n="shippingcharges">Shipping Charges</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.shippingcharges.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.shippingcharges.index') }}" class="menu-link">
                        <div data-i18n="View shippingcharges">View Shipping Charges</div> {{-- More descriptive text --}}
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.shippingcharges.create') || request()->routeIs('admin.shippingcharges.edit') ? 'active' : '' }}">
                    <a href="{{ route('admin.shippingcharges.create') }}" class="menu-link">
                        <div data-i18n="Add New Shipping Charges">Add Shipping Charges</div>
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="menu-item {{ request()->routeIs('admin.enquiries.*') ? 'active' : '' }}">
            <a href="{{ route('admin.enquiries.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-help-circle"></i>
                <div data-i18n="Dashboard">Enquiries</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.videos.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-video-plus"></i> 
                <div data-i18n="videos">Video</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.videos.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.videos.index') }}" class="menu-link">
                        <div data-i18n="View videos">View Video</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.videos.create') || request()->routeIs('admin.videos.edit') ? 'active' : '' }}">
                    <a href="{{ route('admin.videos.create') }}" class="menu-link">
                        <div data-i18n="Add New Video">Add Video</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif
    </ul>
</aside>