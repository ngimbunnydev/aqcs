/**************************************************************************/
/*******************************Input Text Spinner**************************/
/**************************************************************************/
/**
* @license jQuery Twitter Bootstrap input text spinner plugin v1.0.0 23/04/2014
* http://www.totpe.ro
*
* Copyright (c) 2014, Iulian Alexe (contact@totpe.ro)
**/

//Use Exemple:

//$(".form-control").inputSpinner({ //options
//		'opacity'	: 0.5,
//		'color'		: 'red',
//		'glyphicon'	: 'glyphicon-refresh'
//});

//OR:

//$(".form-control").inputSpinner();
(function($) {
	var inputSpinnerFunc = function(element, options) {
		this.$element = $(element);

		if (!(this.$element.is("input[type=text]") || this.$element.is("input[type=password]"))) {
			console.log("Element must bee input type text or password");
			return false;
		}

		this.options = $.extend(true, {}, $.fn.inputSpinner.defaults, options);
		if (this.$element.parents().hasClass("has-feedback1")) {
			this.$element
				.next("span")
				.remove();
			this.$element
				.parents()
				.removeClass("has-feedback1");
		} else {
			this.$element
				.after($("<span></span>")
					.addClass("glyphicon input-spinner form-control-feedback")
					.addClass(this.options.glyphicon)
					.css({
						'opacity': this.options.opacity,
						'color': this.options.color,
						'margin-right': this.options.marginright,
						'float': 'right'
					})
				);
			this.$element
				.parents()
				.addClass("has-feedback1");
		}
		return this;
	};

	var removeSpinnerFunc = function(element, options) {
		this.$element = $(element);

		if (this.$element.parents().hasClass("has-feedback1")) {
			this.$element
				.next("span")
				.remove();
			this.$element
				.parents()
				.removeClass("has-feedback1");
		}
		return this;
	};


	$.fn.inputSpinner = function(options) {
		return new inputSpinnerFunc(this, options);
	};

	$.fn.removeSpinner = function(options){
		return new removeSpinnerFunc(this, options);
	};

	$.fn.inputSpinner.defaults = {
		opacity		: 0.7,
		color		: '#008000',
		glyphicon	: 'glyphicon-refresh',
	};

})(window.jQuery);
