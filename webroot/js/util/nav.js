(function() {
  var toggleNav = function(event) {
    var target = document.getElementById(event.target.dataset.target);
    target.classList.toggle('is-active');
    event.target.classList.toggle('is-active');
  };

  var burgers = document.querySelectorAll(".navbar-burger");

  if (burgers.length === 0) {
    return;
  }

  for (var i = 0; i < burgers.length; i++) {
    burgers.item(i).addEventListener("click", toggleNav);
  }
})();
