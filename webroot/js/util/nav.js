(function() {
  // look for a burger to activate the mobile nav
  var burgers = document.querySelectorAll(".navbar-burger");

  // what the hell are we even doing here?
  if (burgers.length === 0) {
    return;
  }

  // shows/hides the nav on mobile
  var toggleNav = function(event) {
    var target = document.getElementById(event.target.dataset.target);
    target.classList.toggle('is-active');
    event.target.classList.toggle('is-active');
  };

  // listen for clicks on burgers to toggle the nav on mobile
  for (var i = 0; i < burgers.length; i++) {
    burgers.item(i).addEventListener("click", toggleNav);
  }
})();
