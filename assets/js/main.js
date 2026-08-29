// Mobile nav toggle
const navToggle = document.querySelector('.nav__toggle');
const navLinks = document.getElementById('primary-nav');
if (navToggle && navLinks) {
  navToggle.addEventListener('click', () => {
    const open = navLinks.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
}

// Booking form: trip type tabs
const tabs = document.querySelectorAll('.booking__tabs [role="tab"]');
const tripInput = document.getElementById('trip-type');
const dropField = document.getElementById('drop-field');
tabs.forEach((tab) => {
  tab.addEventListener('click', () => {
    tabs.forEach((t) => t.setAttribute('aria-selected', 'false'));
    tab.setAttribute('aria-selected', 'true');
    if (tripInput) tripInput.value = tab.dataset.trip;
    if (dropField) dropField.style.display = tab.dataset.trip === 'Local / Hourly Rental' ? 'none' : '';
  });
});

// Default booking date = today
const dateInput = document.getElementById('pickup-date');
if (dateInput && !dateInput.value) {
  dateInput.value = new Date().toISOString().slice(0, 10);
}

// Fare estimator
const feRoute = document.getElementById('fe-route');
const feDistance = document.getElementById('fe-distance');
const feCar = document.getElementById('fe-car');
const feTrip = document.getElementById('fe-trip');
const feAmount = document.getElementById('fe-amount');
const feBreakdown = document.getElementById('fe-breakdown');
const feBook = document.getElementById('fe-book');

function updateFare() {
  if (!feDistance || !feCar || !feAmount) return;
  const km = Math.max(1, parseInt(feDistance.value || '0', 10));
  const rate = parseInt(feCar.value, 10);
  const mult = feTrip ? parseInt(feTrip.value, 10) : 1;
  const selected = feCar.options[feCar.selectedIndex];
  const perTrip = parseInt(selected.dataset.allowance || feAmount.dataset.allowance || '300', 10);
  const allowance = perTrip * mult;
  const total = km * rate * mult + allowance;
  feAmount.textContent = '₹' + total.toLocaleString('en-IN');
  if (feBreakdown) {
    feBreakdown.textContent =
      km + ' km × ₹' + rate + '/km' + (mult === 2 ? ' × 2 (round trip)' : '') + ' + ₹' + allowance + ' driver allowance';
  }
}
if (feRoute) {
  feRoute.addEventListener('change', () => {
    if (feRoute.value) feDistance.value = feRoute.value;
    updateFare();
  });
}
[feDistance, feCar, feTrip].forEach((el) => el && el.addEventListener('input', updateFare));
updateFare();

if (feBook) {
  feBook.addEventListener('click', () => {
    const carText = feCar.options[feCar.selectedIndex].text;
    const tripText = feTrip.options[feTrip.selectedIndex].text;
    const routeText = feRoute && feRoute.value ? feRoute.options[feRoute.selectedIndex].text : feDistance.value + ' km';
    const msg =
      'Hello MK Cab Service, I would like to book: ' + routeText +
      ' | Car: ' + carText + ' | Trip: ' + tripText + ' | Estimated: ' + feAmount.textContent;
    window.open('https://wa.me/' + (feBook.dataset.wa || '') + '?text=' + encodeURIComponent(msg), '_blank');
  });
}
