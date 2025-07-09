<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
        <div class="sidebar-brand-text mx-3">School Management</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
        <a class="nav-link" href="/">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Menu Heading -->
    <div class="sidebar-heading">
        {{ session('user') && session('user')['role'] === 'Teacher' ? 'Teacher' : 'Admin' }} Menu
    </div>

    @if(!(session('user') && session('user')['role'] === 'Teacher'))
    <!-- Teachers Menu (Only for Admin) -->
    <li class="nav-item {{ request()->is('teachers*') ? 'active' : '' }}">
        <a class="nav-link" href="/teachers">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>Teachers</span>
        </a>
    </li>
    @endif

    @php
        $user = session('user');
        $isTeacher = $user && $user['role'] === 'Teacher';
        $hasClasses = $isTeacher ? \App\Models\Classes::where('teacher_id', $user['id'])->exists() : true;
    @endphp

    @if($hasClasses)
    <!-- Subjects Menu -->
    <li class="nav-item {{ request()->is('subjects*') ? 'active' : '' }}">
        <a class="nav-link" href="/subjects">
            <i class="fas fa-fw fa-book"></i>
            <span>Subjects</span>
        </a>
    </li>

    <!-- Classes Menu -->
    <li class="nav-item {{ request()->is('classes*') ? 'active' : '' }}">
        <a class="nav-link" href="/classes">
            <i class="fas fa-fw fa-school"></i>
            <span>My Classes</span>
        </a>
    </li>

    <!-- Students Menu -->
    <li class="nav-item {{ request()->is('students*') ? 'active' : '' }}">
        <a class="nav-link" href="/students">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>My Students</span>
        </a>
    </li>
    @elseif($isTeacher)
    <!-- Message for teachers with no classes -->
    <li class="nav-item">
        <div class="nav-link text-white-50">
            <i class="fas fa-fw fa-info-circle"></i>
            <span>No classes assigned</span>
        </div>
    </li>
    @endif



    <!-- Menu items are now conditionally shown above based on user role -->

    @if(session('user.role') === 'Teacher')

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <div class="sidebar-heading">
        Attendance Menu
    </div>


    <!-- Attendance Menu -->
    <li class="nav-item {{ request()->is('attendance') || request()->is('attendance/classPicker') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('attendance.classPicker') }}">
            <i class="fas fa-fw fa-calendar-check"></i>
            <span>Attendance</span>
        </a>
    </li>

    <!-- Attendance History Menu -->
    <li class="nav-item {{ request()->is('attendance/history*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('attendance.history') }}">
            <i class="fas fa-fw fa-history"></i>
            <span>History</span>
        </a>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

</ul>
<!-- End of Sidebar -->
