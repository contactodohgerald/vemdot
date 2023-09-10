@extends('frontend.layout') 

@section('content')

    <!-- Start Hero Area -->
    <section id="hero-area" class="hero-area position-relative" >
        <!-- Single Slider -->
        <img src="{{asset('/landing/assets/images/bg.svg')}}" class="position-absolute top-0 start-0 img-fluid" />
        <div class="hero-inner">
            <div class="container">
                <div class="row ">
                    <div class="col-lg-6 co-12">
                        <div class="home-slider">
                            <div class="hero-text">
                                <h5 class="wow fadeInUp" data-wow-delay=".3s">Introducing {{ $appSettings->site_name }}</h5>
                                <h1 class="wow fadeInUp" data-wow-delay=".5s">If you can think about the food, we can deliver it to your Doorstep.</h1>
                                <p class="wow fadeInUp" data-wow-delay=".7s">Order food and groceries for yourself and loved ones from your favorite <br/> food vendors and get them delivered to your door step on time</p>
                                <div class="button wow fadeInUp" data-wow-delay=".9s">
                                    <a href="#" class="btn"><i class="lni lni-android-original"></i> Play Store</a>
                                    <a href="#" class="btn primary"><i class="lni lni-apple"></i> App Store</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="hero-image">
                            <div class="waves-block">
                                <div class="waves wave-1"></div>
                                <div class="waves wave-2"></div>
                            </div>
                            <img src="{{asset('landing/images/vemdot.png')}}" style="width: 50%; " alt="#">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ End Single Slider -->
    </section>
    <!--/ End Hero Area -->

    <!-- Start Features Area -->
    <section id="features" class="features section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <!-- <span class="wow fadeInDown" data-wow-delay=".2s">Food .</span> -->
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">Why choose {{ $appSettings->site_name }} ?</h2>
                        <p class="wow fadeInUp" data-wow-delay=".6s">Discover the best food vendors in your locality. Get started in minutes.</p>
                    </div>
                </div>
            </div>
            <div class="single-head">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <!-- Start Single Feature -->
                        <div class="single-feature wow fadeInUp" data-wow-delay=".2s">
                            <div class="feature-icon">
                                <i class="lni lni-ticket"></i>
                            </div>
                            <h3>Get food how you want it</h3>
                            <p>Enjoy special deals and offers on food and drinks from a vast choice of amazing vendors.</p>
                        </div>
                        <!-- End Single Feature -->
                    </div>
                    <div class="col-md-4 col-12">
                        <!-- Start Single Feature -->
                        <div class="single-feature wow fadeInUp" data-wow-delay=".4s">
                            <div class="feature-icon">
                                <i class="lni lni-timer"></i>
                            </div>
                            <h3>Get food when you want it</h3>
                            <p>You can order and pick up the food at any resturant of your choice.</p>
                        </div>
                        <!-- End Single Feature -->
                    </div>
                    <div class="col-md-4 col-12">
                        <!-- Start Single Feature -->
                        <div class="single-feature wow fadeInUp" data-wow-delay=".6s">
                            <div class="feature-icon">
                                <i class="lni lni-delivery"></i>
                            </div>
                            <h3>Get food where you want it</h3>
                            <p>Track you order and get your favourite food delivered to you in a flash.</p>
                        </div>
                        <!-- End Single Feature -->
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center flex-column flex-md-row align-items-center gap-md-5 gap-2 pt-5">
                <div class="button">
                    <a href="#" class="btn">
                        <i class="lni lni-play-store"></i>
                        <span>Download on</span>
                        Google Play
                    </a>
                </div>
                <div class="button">
                    <a href="#" class="btn">
                        <i class="lni lni-apple"></i>
                        <span>Download on the</span>
                        Apps Store
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- /End Features Area -->

    <!-- Start Cta Area -->
    <section id="call-action" class="call-action section py-0">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-12">
                    <div class="inner-content h-100 d-flex flex-column justify-content-center text-start py-5">
                        <h2 class="wow fadeInUp text-start" data-wow-delay=".4s">Want to earn more as <br>a food Vendor?</h2>
                        <p class="wow fadeInUp text-start" style="text-align: start;" data-wow-delay=".6s">Download our food vendor app to sell food on our platform</p>
                        <div class="button style3 wow fadeInUp text-start" data-wow-delay=".8s">
                            <a href="#" class="btn">Become a Vendor</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 text-center">
                    <img src="{{asset('landing/assets/images/vemdot-vendor.png')}}" style="width: 100%;"  alt="">
                </div>
            </div>
        </div>
    </section>
    <!-- End Cta Area -->

    <!-- Start App Download Area -->
    <section id="download" class="app-download section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <span class="wow fadeInDown" data-wow-delay=".2s">Get the apps</span>
                        <h2 class="wow fadeInUp" data-wow-delay=".4s">Download {{ $appSettings->site_name }} on your Smartphone</h2>
                        <p class="wow fadeInUp" data-wow-delay=".6s">Lorem ipsum dolor sit amet, consectetur adipisicing
                            elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="single-download wow fadeInUp" data-wow-delay=".2s">
                        <h3 class="counter">Download from Google Play</h3>
                        <p>The limited liability is, in fact, the only type of company</p>
                        <div class="button">
                            <a href="#" class="btn">
                                <i class="lni lni-play-store"></i>
                                <span>get it on</span>
                                Google Play
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="single-download wow fadeInUp" data-wow-delay=".4s">
                        <h3 class="counter">Download from Apple Store</h3>
                        <p>The limited liability is, in fact, the only type of company</p>
                        <div class="button">
                            <a href="#" class="btn">
                                <i class="lni lni-apple"></i>
                                <span>Download on the</span>
                                Apps Store
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End App Download Area -->



@endsection