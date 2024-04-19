<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Teknik Informatika</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">

  <!-- Styles -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#FFFFEF]  mx-auto">


  <nav class="text-black  px-4 mx-auto xl:px-20 py-6 w-full bg-[#FFFFEF] top-0 left-0 fixed z-[9999]">
    <div class="flex flex-wrap items-center justify-between mx-auto md:text-lg">
      <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse ">
        <img src="{{ asset('images/logo-teknikinformatika.png') }}" alt="Logo Teknik Informatika"
          class="w-[200px] h-[50px] object-contain md:max-lg:hidden" />
      </a>
      <button data-collapse-toggle="navbar-dropdown" type="button"
        class="inline-flex items-center justify-center w-10 h-10 p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 "
        aria-controls="navbar-dropdown" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M1 1h15M1 7h15M1 13h15" />
        </svg>
      </button>
      <div class="hidden w-full max-lg:mx-auto md:block md:w-auto" id="navbar-dropdown">
        <ul
          class="flex flex-col p-4 mt-4 font-medium rounded-lg md:p-0 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 max-md:bg-yellow-500 max-md:text-white">
          <li>
            <a href="#" class="block px-3 py-2 font-semibold rounded md:bg-transparent md:p-0"
              aria-current="page">Beranda</a>
          </li>
          <li>
            <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar1"
              class="flex items-center justify-between w-full px-3 py-2 font-semibold rounded hover:bg-yellow-400 md:hover:bg-transparent md:border-0 md:p-0 md:w-auto ">Profil
              <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 4 4 4-4" />
              </svg></button>
            <!-- Dropdown menu -->
            <div id="dropdownNavbar1"
              class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 ">
              <ul class="py-2 text-sm text-gray-700 " aria-labelledby="dropdownLargeButton">
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Dashboard</a>
                </li>
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Settings</a>
                </li>
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Earnings</a>
                </li>
              </ul>
              <div class="py-1">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 ">Sign
                  out</a>
              </div>
            </div>
          </li>
          <li>
            <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar2"
              class="flex items-center justify-between w-full px-3 py-2 font-semibold rounded hover:bg-yellow-400 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto ">Akademik
              <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 4 4 4-4" />
              </svg></button>
            <!-- Dropdown menu -->
            <div id="dropdownNavbar2"
              class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 ">
              <ul class="py-2 text-sm text-gray-700 " aria-labelledby="dropdownLargeButton">
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Dashboard</a>
                </li>
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Settings</a>
                </li>
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Earnings</a>
                </li>
              </ul>
              <div class="py-1">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 ">Sign
                  out</a>
              </div>
            </div>
          </li>
          <li>
            <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar3"
              class="flex items-center justify-between w-full px-3 py-2 font-semibold rounded hover:bg-yellow-400 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto ">Panduan
              & SOP
              <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 1 4 4 4-4" />
              </svg></button>
            <!-- Dropdown menu -->
            <div id="dropdownNavbar3"
              class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 ">
              <ul class="py-2 text-sm text-gray-700 " aria-labelledby="dropdownLargeButton">
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Dashboard</a>
                </li>
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Settings</a>
                </li>
                <li>
                  <a href="#" class="block px-4 py-2 hover:bg-gray-100 ">Earnings</a>
                </li>
              </ul>
              <div class="py-1">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 ">Sign
                  out</a>
              </div>
            </div>
          </li>
          <li>
            <a href="#"
              class="block px-3 py-2 font-semibold rounded hover:bg-yellow-400 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 ">Pengumuman</a>
          </li>
          <li>
            <a href="#"
              class="block px-3 py-2 font-semibold rounded hover:bg-yellow-400 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 ">Berita</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class=" sm:mt-12 lg:mt-14">
    @yield('main-no-padding')
  </div>

  <div class="w-full px-4 mx-auto mt-28 max-w-[1400px]">
    @yield('main')
  </div>

</body>

</html>
