 <style>
/* =================================================================
   BASE STYLES
   ================================================================= */
ul {
    margin: 0 0 1.5rem;
    padding: 0;
}

li {
    display: inline-block;
}

svg {
    fill: currentColor;
    width: 1.5rem;
    height: 1.5rem;
}

main {
    max-width: 81.25rem;
    margin: auto;
}

/* =================================================================
   COMPONENTS
   ================================================================= */
.bord-nomads {
    border-top-radius: 5px;
}

.card {
    border-radius: 5px;
    background-color: #fff;
    min-height: 530px;
}

.card-content {
    padding: 1.9375rem;
}

/* =================================================================
   CAROUSEL STYLES
   ================================================================= */
.carousel-items {
    display: flex;
    overflow-x: scroll;
    padding: 1rem 0;
    scroll-snap-type: x mandatory;
}

.carousel-item {
    flex: 1 0 250px;
    margin: 0 1rem;
    scroll-snap-align: start;
}

/* Scrollbar styles */
::-webkit-scrollbar {
    height: 6px;
    background-color: #f5f5f5;
}

::-webkit-scrollbar-track {
    background-color: #f5f5f5;
}

::-webkit-scrollbar-thumb {
    background-color: #3d4852;
    border-radius: 3px;
}

/* =================================================================
   TYPOGRAPHY
   ================================================================= */
.hhnomads-01 {
    font-size: 30px;
    letter-spacing: 2px;
    line-height: 1.0;
    text-align: center!important;
}

.hhnomads-02 {
    font-size: 39px;
    letter-spacing: 2px;
    line-height: 1.0;
    text-align: center!important;
}

/* =================================================================
   RESPONSIVE DESIGN
   ================================================================= */
/* Mobile first (0px+) */
@media screen and (min-width: 0px) {
    .carousel-item { flex-basis: 520px; }
}

/* Small devices (576px+) */
@media screen and (min-width: 576px) {
    .carousel-item { flex-basis: 400px; }
    .hhnomads-01 { font-size: 29px; text-align: left; }
    .hhnomads-02 { font-size: 37px; text-align: left; }
}

/* Medium devices (992px+) */
@media screen and (min-width: 992px) {
    .carousel-item { flex-basis: 380px; }
    .hhnomads-01 { font-size: 27px; }
    .hhnomads-02 { font-size: 35px; }
}

/* Large devices (1280px+) */
@media screen and (min-width: 1280px) {
    .carousel-item { flex-basis: 410px; }
    .card { min-height: 580px; }
    .hhnomads-01 { font-size: 30px; }
    .hhnomads-02 { font-size: 39px; }
}

/* Extra large devices (1600px+) */
@media screen and (min-width: 1600px) {
    .carousel-item { flex-basis: 410px; }
    .card { min-height: 580px; }
    .hhnomads-01 { font-size: 30px; }
    .hhnomads-02 { font-size: 39px; }
}
    </style>


 <h3 style="padding-bottom: 20px;" class="text-center">SERVICES AND USEFUL INFO</h3>
    <main>
        <section class="carousel">
            <ul class="carousel-items">
                <li class="carousel-item">
                    <div class="card">
                        <img class="bord-nomads" alt="Ibiza Villas 2000 - Ibiza Airport Transfers" title="Ibiza Villas 2000 - Ibiza Airport Transfers" src="https://ibizavillas2000.com/wp-content/themes/rudeibiza/services/IbizaVillas2000-AirportTransf.jpg" />
                        <div class="card-content">
                            <h3><a style="color:#1B5167;" href="https://ibizavillas2000.com/villa-taxi-service/" title="Ibiza Villas 2000 - Ibiza Airport Transfers">AIRPORT TRANSFERS</a></h3>
                            <p style="color:#37B89A;" class="slider-location-sleeps"><i style="color:#37B89A;padding-right:10px;" class="fas fa-car"></i>Ibiza Villas Services </p>
                            <p>We work with reliable partners to provide a seamless transfer service to all our villas on the island.</p>
                            <a style="padding:10px;width: 100%!important;" href="https://ibizavillas2000.com/villa-taxi-service/" class="button butds" data-hover="IBIZA VILLAS SERVICES">LEARN MORE</a>
                        </div>
                    </div>
                </li>
                
                <li class="carousel-item">
                    <div class="card">
                        <img class="bord-nomads" alt="Ibiza Villas 2000 - Ibiza Boat Hire" title="Ibiza Villas 2000 - Ibiza Boat Hire" src="https://ibizavillas2000.com/wp-content/themes/rudeibiza/services/IbizaVillas2000-BoatHire.jpg" />
                        <div class="card-content">
                            <h3><a style="color:#1B5167;" href="https://ibizavillas2000.com/ibiza-boat-charter/" title="Ibiza Villas 2000 - Ibiza Boat Hire">IBIZA BOAT HIRE</a></h3>
                            <p style="color:#37B89A;" class="slider-location-sleeps"><i style="color:#37B89A;padding-right:10px;" class="fas fa-ship"></i>Ibiza Villas Services </p>
                            <p>We work with the best boat chartering companies on the island, hands down!</p>
                            <a style="padding:10px;width: 100%!important;" href="https://ibizavillas2000.com/ibiza-boat-charter/" class="button butds" data-hover="IBIZA VILLAS SERVICES">LEARN MORE</a>
                        </div>
                    </div>
                </li>

                <li class="carousel-item">
                    <div style="background-color: #F6B91D;" class="card">
                        <img class="bord-nomads" alt="Ibiza Villas 2000 - Rental Insider Guide" title="Ibiza Villas 2000 - Ibiza Rental Insider Guide" src="https://ibizavillas2000.com/wp-content/themes/rudeibiza/services/IbizaVillas2000-IbizaRentalGuide.png" />
                        <div style="padding-top: 0px!important;" class="card-content">
                            <h3><a class="hhnomads-01" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">IBIZA VILLA RENTAL</a></h3>
                            <h3><a class="hhnomads-02" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">INSIDERS GUIDE</a></h3>
                            <h3><a class="hhnomads-01" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">FOR YOUR HOLIDAY</a></h3>
                            <a style="margin-top:20px;padding:10px;width: 100%!important;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/" class="button butds" data-hover="MORE INFO">IBIZA RENTAL GUIDE</a>
                        </div>
                    </div>
                </li>

                <li class="carousel-item">
                    <div class="card">
                        <img class="bord-nomads" alt="Ibiza Villas 2000 - Ibiza Car Hire" title="Ibiza Villas 2000 - Ibiza Car Hire" src="https://ibizavillas2000.com/wp-content/themes/rudeibiza/services/IbizaVillas2000-CarRent.jpg" />
                        <div class="card-content">
                            <h3><a style="color:#1B5167;" href="https://ibizavillas2000.com/ibiza-car-hire/" title="Ibiza Villas 2000 - Ibiza Car Hire">IBIZA CAR HIRE</a></h3>
                            <p style="color:#37B89A;" class="slider-location-sleeps"><i style="color:#37B89A;padding-right:10px;" class="fas fa-key"></i>Ibiza Villas Services </p>
                            <p>We work with a local company that will deliver the car to your villa and won't charge you through...</p>
                            <a style="padding:10px;width: 100%!important;" href="https://ibizavillas2000.com/ibiza-car-hire/" class="button butds" data-hover="MORE INFO">LEARN MORE</a>
                        </div>
                    </div>
                </li>

                <li class="carousel-item">
                    <div class="card">
                        <img class="bord-nomads" alt="Ibiza Villas 2000 - Check In - Check Out" title="Ibiza Villas 2000 - Ibiza Airport Transfers" src="https://ibizavillas2000.com/wp-content/themes/rudeibiza/services/IbizaVillas2000-EarlyCheckOut.jpg" />
                        <div class="card-content">
                            <h3><a style="color:#1B5167;" href="https://ibizavillas2000.com/check-in-check-out/" title="Ibiza Villas 2000 - Check In - Check Out">LATE CHECK OUT</a></h3>
                            <p style="color:#37B89A;" class="slider-location-sleeps"><i style="color:#37B89A;padding-right:10px;" class="fas fa-bed"></i>Ibiza Villas Services </p>
                            <p>After a few days of indulging in all that Ibiza has to offer or feeling that air of excitement at the very start of a holiday...</p>
                            <a style="padding:10px;width: 100%!important;" href="https://ibizavillas2000.com/check-in-check-out/" class="button butds" data-hover="MORE INFO">LEARN MORE</a>
                        </div>
                    </div>
                </li>

                <li class="carousel-item">
                    <div class="card">
                        <img class="bord-nomads" alt="Ibiza Villas 2000 - Beauty Treatments" title="Ibiza Villas 2000 - Beauty Treatments" src="https://ibizavillas2000.com/wp-content/themes/rudeibiza/services/IbizaVillas2000-Massage.jpg" />
                        <div class="card-content">
                            <h3><a style="color:#1B5167;" href="https://ibizavillas2000.com/massage-beauty-treatments/" title="Ibiza Villas 2000 - Beauty Treatments">BEAUTY TREATMENTS</a></h3>
                            <p style="color:#37B89A;" class="slider-location-sleeps"><i style="color:#37B89A;padding-right:10px;" class="fas fa-child"></i>Ibiza Villas Services </p>
                            <p>Whether it's a big night to prepare for or some peace and serenity that you're after, look no further.</p>
                            <a style="padding:10px;width: 100%!important;" href="https://ibizavillas2000.com/massage-beauty-treatments/" class="button butds" data-hover="MORE INFO">LEARN MORE</a>
                        </div>
                    </div>
                </li>
            </ul>
        </section>
    </main>