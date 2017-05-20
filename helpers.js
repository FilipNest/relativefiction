module.exports = function (Handlebars) {

  Handlebars.registerHelper('equals', function (lvalue, rvalue, options) {

    if (arguments.length < 3) {
      throw new Error("equals needs two values to compare");
    }

    // Expression version

    if (!options.fn) {

      return lvalue == rvalue;

    }

    // Block version

    if (lvalue == rvalue) {
      return options.fn(this);
    } else {
      return options.inverse(this);
    }

  });

  Handlebars.registerHelper('not', function (lvalue, rvalue, options) {

    if (arguments.length < 3) {
      throw new Error("not needs two values to compare");
    }

    // Expression version

    if (!options.fn) {

      return lvalue != rvalue;

    }

    // Block version

    if (lvalue != rvalue) {
      return options.fn(this);
    } else {
      return options.inverse(this);
    }

  });

  Handlebars.registerHelper('less', function (lvalue, rvalue, options) {

    if (arguments.length < 3) {
      throw new Error("less needs two values to compare");
    }

    // Expression version

    if (!options.fn) {

      return lvalue < rvalue;

    }

    // Block version

    if (lvalue < rvalue) {
      return options.fn(this);
    } else {
      return options.inverse(this);
    }

  });

  Handlebars.registerHelper('more', function (lvalue, rvalue, options) {

    if (arguments.length < 3) {
      throw new Error("more needs two values to compare");
    }

    // Expression version

    if (!options.fn) {

      return lvalue > rvalue;

    }

    // Block version

    if (lvalue > rvalue) {
      return options.fn(this);
    } else {
      return options.inverse(this);
    }

  });

  Handlebars.registerHelper('and', function (lvalue, rvalue, options) {

    if (arguments.length < 3) {
      throw new Error("and needs two values to compare");
    }

    // Expression version

    if (!options.fn) {

      return lvalue && rvalue;

    }

    // Block version

    if (lvalue && rvalue) {
      return options.fn(this);
    } else {
      return options.inverse(this);
    }

  });

  Handlebars.registerHelper('or', function (lvalue, rvalue, options) {

    if (arguments.length < 3) {
      throw new Error("or needs two values to compare");
    }

    // Expression version

    if (!options.fn) {

      return lvalue || rvalue;

    }

    // Block version

    if (lvalue || rvalue) {
      return options.fn(this);
    } else {
      return options.inverse(this);
    }

  });


}
