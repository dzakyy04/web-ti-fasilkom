@extends('layouts.main')

@section('main-no-padding')
  <div id="default-carousel" data class="relative w-full" data-carousel-interval="5000" data-carousel="slide">
    <!-- Carousel wrapper -->
    <div class="relative overflow-hidden rounded-2xl h-[400px] xl:min-h-screen">
      <!-- Item 1 -->
      <div class="hidden w-3/4 px-3 mx-auto duration-700 ease-in-out rounded-2xl" id="carousel-item-1" data-carousel-item>
        <img src="{{ asset('images/carousel-image.png') }}"
          class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 rounded-2xl" alt="...">
      </div>
      <!-- Item 2 -->
      <div class="hidden w-3/4 mx-auto duration-700 ease-in-out rounded-2xl" id="carousel-item-2" data-carousel-item>
        <img src="{{ asset('images/carousel-image.png') }}"
          class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 rounded-2xl" alt="...">
      </div>
      <!-- Item 3 -->
      <div class="hidden w-3/4 mx-auto duration-700 ease-in-out rounded-2xl" id="carousel-item-3" data-carousel-item>
        <img src="{{ asset('images/carousel-image.png') }}"
          class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 rounded-2xl" alt="...">
      </div>
    </div>
    <!-- Slider indicators -->
    <div class="absolute z-30 flex space-x-3 -translate-x-1/2 bottom-5 left-1/2 rtl:space-x-reverse">
      <button type="button" class="w-3 h-3 rounded-full" aria-current="true" aria-label="Slide 1"
        data-carousel-slide-to="0"></button>
      <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 2"
        data-carousel-slide-to="1"></button>
      <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 3"
        data-carousel-slide-to="2"></button>
    </div>
    <!-- Slider controls -->
    <button type="button"
      class="absolute top-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer start-0 group focus:outline-none"
      data-carousel-prev>
      <span
        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
        <svg class="w-4 h-4 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4" />
        </svg>
        <span class="sr-only">Previous</span>
      </span>
    </button>
    <button type="button"
      class="absolute top-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer end-0 group focus:outline-none"
      data-carousel-next>
      <span
        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
        <svg class="w-4 h-4 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
          fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
        </svg>
        <span class="sr-only">Next</span>
      </span>
    </button>
  </div>
@endsection
