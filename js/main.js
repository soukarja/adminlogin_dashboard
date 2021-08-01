(function($) {

	"use strict";


})(jQuery);




$(".passwordBox > .toggles > .unhide").click(function() {
    this.style.display = "none";
    this.nextElementSibling.style.display = "flex";

    var passBox = this.parentNode.previousElementSibling;
    passBox.type="text";
  });

  $(".passwordBox > .toggles > .hide").click(function() {
    this.style.display = "none";
    this.previousElementSibling.style.display = "flex";

    var passBox = this.parentNode.previousElementSibling;
    passBox.type="password";
  });
