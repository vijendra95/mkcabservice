<?php
// Default editable content for every page. The /admin panel shows these values
// and saves overrides to data/pages/<page>.json. Tokens available in any field:
// {{PHONE}} {{EMAIL}} {{ADDRESS}} {{WHATSAPP_LINK}} {{SITE_NAME}}
return [
    'home' => [
        'label' => 'Home page',
        'fields' => [
            'hero_title' => 'Safe, reliable &amp; <span class="hl">on-time</span> cabs in Jaipur — book in minutes',
            'hero_sub' => 'Well-kept cars, honest fares and courteous, verified drivers. Outstation trips, airport transfers, local rentals and Rajasthan tours — available round the clock.',
        ],
    ],
    'about' => [
        'label' => 'About Us',
        'fields' => [
            'hero_title' => 'About MK Cab Service',
            'hero_sub' => 'A Jaipur-grown cab company built on three simple things: safe drivers, clean cars and a fare you can trust.',
            'body' => <<<'HTML'
<h2>Who we are</h2>
<p>MK Cab Service is a taxi and car rental service based in VKI, Jaipur. We started with a handful of cars and one rule &mdash; treat every rider the way we'd want our own family driven &mdash; and that rule still decides how we hire drivers, maintain cars and quote fares today.</p>
<h2>What we do</h2>
<ul>
<li>Outstation one-way and round-trip cabs across Rajasthan and North India</li>
<li>Jaipur airport pickups and drops, 24x7 with flight tracking</li>
<li>Local hourly rentals for sightseeing, weddings and business</li>
<li>Multi-day Rajasthan tour packages with dedicated car and driver</li>
</ul>
<h2>Our mission &mdash; comfortable travel you can trust</h2>
<p>We believe you shouldn't have to choose between affordability, comfort and safety. That's why we run a well-serviced, fuel-efficient fleet and quote honest, all-inclusive fares &mdash; so every kilometre is easy on your pocket and on your mind.</p>
<ul>
<li><strong>Honest fares:</strong> transparent pricing with no surge and no hidden charges</li>
<li><strong>Real comfort:</strong> sanitised, AC cars with ample legroom</li>
<li><strong>Safety always:</strong> verified drivers and well-serviced cars, day or night</li>
<li><strong>24x7 reliable:</strong> on-time pickups whenever you need us</li>
</ul>
<h2>Why riders stay with us</h2>
<p>Because the quote we give is the fare you pay. Because the driver calls you before you have to call him. And because the car that arrives is the car you booked &mdash; clean, serviced and on time.</p>
<h2>Visit or message us</h2>
<p>{{ADDRESS}}<br>
WhatsApp: <a style="color:var(--orange-600);font-weight:700" href="{{WHATSAPP_LINK}}" target="_blank" rel="noopener">{{PHONE}}</a><br>
Email: <a style="color:var(--orange-600);font-weight:700" href="mailto:{{EMAIL}}">{{EMAIL}}</a></p>
HTML,
        ],
    ],
    'outstation' => [
        'label' => 'Outstation Cabs',
        'fields' => [
            'hero_title' => 'Outstation Cabs from Jaipur',
            'hero_sub' => 'One-way and round-trip taxis from Jaipur to Delhi, Udaipur, Agra, Jodhpur and every major city in Rajasthan and North India — at fair per-km rates with verified drivers.',
            'body' => <<<'HTML'
<h2>Where can I go?</h2>
<p>Anywhere in Rajasthan and North India. Our most popular routes from Jaipur are Delhi, Udaipur, Agra, Jodhpur, Ajmer, Jaisalmer, Bikaner, Kota, Mount Abu and Ranthambore &mdash; but if your destination has a road, we will take you there.</p>
<h2>How is the fare calculated?</h2>
<p>Fares are based on distance and car type: Sedan from &#8377;11/km, SUV from &#8377;15/km, Innova Crysta from &#8377;19/km and Tempo Traveller from &#8377;35/km, plus a fixed driver allowance (&#8377;500 for Tempo Traveller). Only parking is billed extra. For round trips, distance is counted both ways.</p>
<h2>When should I book?</h2>
<p>You can book any time &mdash; we run 24x7. For early-morning departures or peak season travel, booking a few hours in advance helps us send you the best car and driver for your route.</p>
<h2>Why choose MK Cab Service for outstation travel?</h2>
<p>Transparent per-km pricing with no hidden charges, clean and fuel-efficient AC cars, verified drivers who know the highways, and 24x7 WhatsApp support from booking to drop. Whether it is a same-day Delhi drop or a week-long Rajasthan circuit, we plan the trip around your schedule.</p>
HTML,
        ],
    ],
    'airport' => [
        'label' => 'Airport Taxi',
        'fields' => [
            'hero_title' => 'Jaipur Airport Taxi',
            'hero_sub' => '24x7 pickup and drop for Jaipur International Airport with flight tracking and on-time guarantee — for red-eye departures and late-night landings alike.',
            'body' => <<<'HTML'
<h2>Airport pickup (arrivals)</h2>
<p>Your driver monitors the flight and is ready at the arrivals area when you land. Just share your flight number and terminal while booking, and keep your phone on after landing.</p>
<h2>Airport drop (departures)</h2>
<p>Tell us your flight time and we will suggest the right pickup slot based on traffic, so you reach with comfortable check-in margin. Early-morning slots are our specialty.</p>
<h2>Outstation airport transfers</h2>
<p>Flying out of Delhi instead? We also run direct Jaipur to Delhi Airport (IGI) drops &mdash; a comfortable 5-6 hour highway ride at a fixed fare.</p>
HTML,
        ],
    ],
    'local' => [
        'label' => 'Local Car Rental',
        'fields' => [
            'hero_title' => 'Local Car Rental in Jaipur',
            'hero_sub' => 'Hourly car rentals with driver for Jaipur sightseeing, shopping runs, weddings and business meetings — flexible 8hr/80km and half-day packages.',
            'body' => <<<'HTML'
<h2>Jaipur sightseeing by car</h2>
<p>Cover Amber Fort, Hawa Mahal, City Palace, Jantar Mantar, Jal Mahal, Albert Hall and the local bazaars comfortably in a day. Our drivers know the timings, parking spots and the right order to beat the crowds.</p>
<h2>How the packages work</h2>
<p>Packages start from your pickup point in Jaipur. Extra hours and kilometres beyond the package are billed at simple, pre-agreed rates &mdash; the driver keeps you informed, so there are no surprises.</p>
<h2>Corporate &amp; event bookings</h2>
<p>Need cars for guests, delegates or a wedding baraat? We handle multi-car bookings with a single point of contact and consolidated billing.</p>
HTML,
        ],
    ],
    'tours' => [
        'label' => 'Tour Packages',
        'fields' => [
            'hero_title' => 'Rajasthan Tour Packages',
            'hero_sub' => 'Multi-day road trips across Rajasthan with a dedicated car and driver — Golden Triangle, lakes of Udaipur, the blue city of Jodhpur and the dunes of Jaisalmer.',
            'body' => <<<'HTML'
<h2>What is included</h2>
<p>A dedicated AC car with an experienced driver for the entire tour, fuel, driver allowance and all driving-related costs. Hotels and monument tickets are on you, though we happily suggest good options for every budget.</p>
<h2>Why travel by cab</h2>
<p>Rajasthan rewards road travel: stepwells, roadside dhabas, camel carts and village markets that trains and flights simply skip. With your own car you stop where you like, for as long as you like.</p>
<h2>Popular add-ons</h2>
<p>Ranthambore tiger safari, Chittorgarh fort, Bundi day-trip, Sambhar salt lake sunset and hot-air ballooning near Amber &mdash; ask us while planning and we will fit them into your route.</p>
HTML,
        ],
    ],
    'fleet' => [
        'label' => 'Fleet & Pricing',
        'fields' => [
            'hero_title' => 'Our Fleet &amp; Pricing',
            'hero_sub' => 'Every car is cleaned before your trip, serviced on schedule and comes with an experienced, verified driver.',
            'body' => <<<'HTML'
<h2>What the rate includes</h2>
<ul>
<li>AC car with fuel and an experienced driver</li>
<li>Driver allowance shown separately in your quote &mdash; no hidden markups</li>
<li>Only parking billed extra</li>
</ul>
<p>WhatsApp <a style="color:var(--orange-600);font-weight:700" href="{{WHATSAPP_LINK}}" target="_blank" rel="noopener">{{PHONE}}</a> for an exact quote for your trip &mdash; we confirm fares before you book, and the price never changes after.</p>
HTML,
        ],
    ],
    'contact' => [
        'label' => 'Contact Us',
        'fields' => [
            'hero_title' => 'Contact Us',
            'hero_sub' => 'WhatsApp us or drop a message — we reply within minutes, 24x7.',
            'body' => <<<'HTML'
<h2>Reach us directly</h2>
<p><b>WhatsApp:</b> <a style="color:var(--orange-600);font-weight:700" href="{{WHATSAPP_LINK}}" target="_blank" rel="noopener">{{PHONE}}</a><br>
<b>Email:</b> <a style="color:var(--orange-600);font-weight:700" href="mailto:{{EMAIL}}">{{EMAIL}}</a></p>
<h2>Office address</h2>
<p>{{ADDRESS}}</p>
<h2>Working hours</h2>
<p>24 hours a day, 7 days a week &mdash; including holidays. Late-night airport pickups and early-morning departures are never a problem.</p>
HTML,
        ],
    ],
    'blog' => [
        'label' => 'Blog (listing page)',
        'fields' => [
            'hero_title' => 'Travel Blog',
            'hero_sub' => 'Route guides, fare explainers and Rajasthan trip ideas — straight from the drivers&#039; seat.',
        ],
    ],
];
