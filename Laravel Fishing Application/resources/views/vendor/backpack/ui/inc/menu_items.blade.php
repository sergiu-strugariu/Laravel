{{-- This file is used for menu items by any Backpack v6 theme --}}
<x-backpack::menu-separator title="Redirectionari" />
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i>Admin Dashboard</a></li>
<li class="nav-item"><a class="nav-link" href="{{route('home')}}"><i class="la la-home nav-icon"></i>Inapoi pe site</a></li>

<x-backpack::menu-separator title="Utilizatori" />
<x-backpack::menu-item title="Utilizatori" icon="las la-user" :link="backpack_url('user')" />
<x-backpack::menu-item title="Palmares" icon="las la-history" :link="backpack_url('palmares')" />

<x-backpack::menu-separator title="Cantarire" />
<x-backpack::menu-item title="Alocare Standuri" icon="la la-link" :link="backpack_url('alocare-stand')" />
<x-backpack::menu-item title="Cantarire" icon="la la-balance-scale" :link="backpack_url('cantar')" />

<x-backpack::enu-separator title="Concursuri" />
<x-backpack::menu-item title="Inscrieri" icon="las la-newspaper" :link="backpack_url('inscriere')" />
<x-backpack::menu-item title="Concursuri" icon="las la-trophy" :link="backpack_url('concurs')" />
<x-backpack::menu-item title="Manse" icon="las la-sitemap" :link="backpack_url('mansa')" />

<x-backpack::menu-separator title="Altele" />
<x-backpack::menu-item title="Standuri" icon="las la-store" :link="backpack_url('stand')" />
<x-backpack::menu-item title="Lacuri" icon="las la-water" :link="backpack_url('lac')" />
<x-backpack::menu-item title="Sectoare" icon="las la-school" :link="backpack_url('sector')" />

