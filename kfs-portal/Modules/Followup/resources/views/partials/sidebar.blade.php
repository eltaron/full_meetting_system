@php
    // روابط بسيطة (بدون قائمة منسدلة) بتظهر بشكل مباشر
    // permission = null معناه الرابط ده ظاهر لأي مستخدم مسجل دخول من غير شرط صلاحية
    $simpleTopLinks = [
        ['route' => 'dashboard', 'icon' => 'fa-th-large', 'label' => 'الصفحة الرئيسية', 'permission' => null],
    ];
@endphp

<aside id="sidebar"
    class="fixed top-0 bottom-0 right-0 w-72 bg-primary text-white z-50 shadow-2xl overflow-y-auto
        transform translate-x-full lg:translate-x-0 transition-transform duration-300">
    <div class="p-6 flex flex-col h-full">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/imgs/watania.png') }}" alt="Logo"
                    class="w-10 h-10 bg-white p-1 rounded-full shadow-lg">
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight whitespace-nowrap text-white">Task Flow</span>
                    <span class="text-[10px] font-bold text-accent">محافظة كفر الشيخ </span>
                </div>
            </div>
            <button id="close-sidebar" class="lg:hidden text-white text-2xl"><i class="fas fa-times"></i></button>
        </div>

        <nav class="flex-1 space-y-2">

            {{-- 1) الصفحة الرئيسية --}}
            @foreach ($simpleTopLinks as $link)
                @if (is_null($link['permission']) || auth()->user()?->can($link['permission']))
                    @php $isActive = Route::has($link['route']) && request()->routeIs($link['route']); @endphp
                    <a href="{{ Route::has($link['route']) ? route($link['route']) : '#' }}"
                        class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300 group
                            {{ $isActive ? 'bg-accent text-white shadow-lg' : 'hover:bg-white/10' }}">
                        <i class="fas {{ $link['icon'] }} w-6 text-center
                            {{ $isActive ? '' : 'text-accent group-hover:scale-110 transition-transform' }}"></i>
                        <span class="font-medium text-lg">{{ $link['label'] }}</span>
                    </a>
                @endif
            @endforeach

            {{-- 2) متابعة التكليفات --}}
            @canany(['tasks.create', 'tasks.view'])
                @php
                    $tasksRoutes = [
                        'tasks.create', 'tasks.index', 'tasks.show', 'tasks.edit', 'tasks.dashboard',
                        'task-sources.*', 'task-entities.*',
                    ];
                    $isTasksActive = request()->routeIs($tasksRoutes);
                @endphp

                <div>
                    <button type="button" onclick="document.getElementById('tasks-submenu').classList.toggle('hidden'); this.querySelector('.tasks-chevron').classList.toggle('rotate-180')"
                        class="w-full flex items-center justify-between gap-4 px-4 py-3 rounded-xl transition-all duration-300 group
                            {{ $isTasksActive ? 'bg-accent text-white shadow-lg' : 'hover:bg-white/10' }}">
                        <span class="flex items-center gap-4">
                            <i class="fas fa-tasks w-6 text-center
                                {{ $isTasksActive ? '' : 'text-accent group-hover:scale-110 transition-transform' }}"></i>
                            <span class="font-medium text-lg">متابعة التكليفات</span>
                        </span>
                        <i class="fas fa-chevron-down text-sm tasks-chevron transition-transform duration-300 {{ $isTasksActive ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="tasks-submenu" class="{{ $isTasksActive ? '' : 'hidden' }} mt-1 mr-4 space-y-1 border-r-2 border-white/10 pr-4">
                        @can('tasks.create')
                            <a href="{{ route('tasks.create') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('tasks.create') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-plus-circle w-5 text-center text-accent"></i>
                                تسجيل تكليف جديد
                            </a>
                        @endcan

                        @can('tasks.view')
                            <a href="{{ route('tasks.index') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('tasks.index') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-list-check w-5 text-center text-accent"></i>
                                قائمة التكليفات
                            </a>
                        @endcan

                        @can('tasks.view')
                            <a href="{{ route('tasks.dashboard') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('tasks.dashboard') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-chart-pie w-5 text-center text-accent"></i>
                                لوحة المتابعة
                            </a>
                        @endcan

                        @can('tasks.view')
                            <a href="{{ route('task-sources.index') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('task-sources.*') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-share-nodes w-5 text-center text-accent"></i>
                                مصادر التكليف
                            </a>
                        @endcan

                        @can('tasks.view')
                            <a href="{{ route('task-entities.index') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('task-entities.*') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-building w-5 text-center text-accent"></i>
                                الجهات التعامل
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany

            {{-- 3) إدارة المستخدمين (قائمة منسدلة: المستخدمون - الأدوار والصلاحيات - الصلاحيات - الصلاحيات المباشرة) --}}
            @canany(['users.manage', 'roles.manage'])
                @php
                    $usersMgmtRoutes = ['admin.users.*', 'admin.roles.*', 'admin.permissions.*', 'admin.user-permissions.*'];
                    $isUsersMgmtActive = request()->routeIs($usersMgmtRoutes);
                @endphp

                <div>
                    <button type="button" onclick="document.getElementById('users-mgmt-submenu').classList.toggle('hidden'); this.querySelector('.users-mgmt-chevron').classList.toggle('rotate-180')"
                        class="w-full flex items-center justify-between gap-4 px-4 py-3 rounded-xl transition-all duration-300 group
                            {{ $isUsersMgmtActive ? 'bg-accent text-white shadow-lg' : 'hover:bg-white/10' }}">
                        <span class="flex items-center gap-4">
                            <i class="fas fa-users w-6 text-center
                                {{ $isUsersMgmtActive ? '' : 'text-accent group-hover:scale-110 transition-transform' }}"></i>
                            <span class="font-medium text-lg">إدارة المستخدمين</span>
                        </span>
                        <i class="fas fa-chevron-down text-sm users-mgmt-chevron transition-transform duration-300 {{ $isUsersMgmtActive ? 'rotate-180' : '' }}"></i>
                    </button>

                    <div id="users-mgmt-submenu" class="{{ $isUsersMgmtActive ? '' : 'hidden' }} mt-1 mr-4 space-y-1 border-r-2 border-white/10 pr-4">
                        @can('users.manage')
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('admin.users.*') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-users w-5 text-center text-accent"></i>
                                إدارة المستخدمين
                            </a>
                        @endcan

                        @can('roles.manage')
                            <a href="{{ route('admin.roles.index') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('admin.roles.*') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-user-shield w-5 text-center text-accent"></i>
                                الأدوار والصلاحيات
                            </a>

                            <a href="{{ route('admin.permissions.index') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('admin.permissions.*') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-key w-5 text-center text-accent"></i>
                                إدارة الصلاحيات
                            </a>

                            <a href="{{ route('admin.user-permissions.index') }}"
                                class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-300 text-sm
                                    {{ request()->routeIs('admin.user-permissions.*') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                                <i class="fas fa-user-lock w-5 text-center text-accent"></i>
                                صلاحيات مباشرة للموظفين
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany
<a href="{{ route('signature-officers.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ request()->routeIs('signature-officers.*') ? 'bg-white/10' : '' }}">
    <i class="fas fa-signature"></i> مسئولو التأشيرات
</a>
        </nav>

        <div class="mt-auto border-t border-white/10 pt-6">
            <button onclick="openLogoutModal()"
                class="w-full flex items-center gap-4 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all duration-300 font-bold group">
                <i class="fas fa-sign-out-alt w-6 text-center group-hover:-translate-x-1 transition-transform"></i>
                <span class="text-lg">تسجيل الخروج</span>
            </button>
        </div>
    </div>
</aside>
